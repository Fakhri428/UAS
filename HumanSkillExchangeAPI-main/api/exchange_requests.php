<?php

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/plan_limits.php";

allowCors("GET, POST, PATCH");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

switch ($method) {
    case "GET":
        $id ? getExchangeRequestById($pdo, $currentUser, $id) : getExchangeRequests($pdo, $currentUser);
        break;

    case "POST":
        createExchangeRequest($pdo, $currentUser);
        break;

    case "PATCH":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        handleExchangePatch($pdo, $currentUser, $id);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getExchangeRequests($pdo, $currentUser)
{
    $stmt = $pdo->prepare("
        SELECT exchange_requests.*,
               from_user.name AS from_user_name,
               to_user.name AS to_user_name,
               offers.title AS offer_title,
               needs.title AS need_title
        FROM exchange_requests
        JOIN users AS from_user ON from_user.id = exchange_requests.from_user_id
        JOIN users AS to_user ON to_user.id = exchange_requests.to_user_id
        LEFT JOIN offers ON offers.id = exchange_requests.offer_id
        LEFT JOIN needs ON needs.id = exchange_requests.need_id
        WHERE exchange_requests.from_user_id = ?
        OR exchange_requests.to_user_id = ?
        ORDER BY exchange_requests.id DESC
    ");
    $stmt->execute([$currentUser["id"], $currentUser["id"]]);

    jsonResponse(200, true, "Data exchange request berhasil diambil", $stmt->fetchAll());
}

function getExchangeRequestById($pdo, $currentUser, $id)
{
    $request = findExchangeRequest($pdo, $currentUser, $id);

    jsonResponse(200, true, "Detail exchange request berhasil diambil", $request);
}

function createExchangeRequest($pdo, $currentUser)
{
    enforceMonthlyExchangeLimit($pdo, $currentUser);

    $input = readJsonInput();
    requireFields($input, ["to_user_id", "offer_id", "need_id", "message"]);

    $toUserId = (int) $input["to_user_id"];
    $offerId = (int) $input["offer_id"];
    $needId = (int) $input["need_id"];
    $message = stringValue($input, "message");

    if ($toUserId === $currentUser["id"]) {
        jsonResponse(422, false, "Tidak bisa mengirim exchange request ke diri sendiri");
    }

    assertUserExists($pdo, $toUserId);
    assertRowExists($pdo, "offers", $offerId, "Offer");
    assertRowExists($pdo, "needs", $needId, "Need");

    $stmt = $pdo->prepare("
        INSERT INTO exchange_requests
            (from_user_id, to_user_id, offer_id, need_id, message, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $currentUser["id"],
        $toUserId,
        $offerId,
        $needId,
        $message
    ]);

    jsonResponse(201, true, "Exchange request berhasil dikirim", [
        "id" => (int) $pdo->lastInsertId(),
        "from_user_id" => $currentUser["id"],
        "to_user_id" => $toUserId,
        "offer_id" => $offerId,
        "need_id" => $needId,
        "status" => "pending",
        "message" => $message
    ]);
}

function handleExchangePatch($pdo, $currentUser, $id)
{
    $action = trim((string) ($_GET["action"] ?? "status"));

    if ($action === "complete") {
        completeExchange($pdo, $currentUser, $id);
    }

    if ($action === "status") {
        updateExchangeStatus($pdo, $currentUser, $id);
    }

    jsonResponse(400, false, "Action tidak valid. Gunakan action=status atau action=complete");
}

function updateExchangeStatus($pdo, $currentUser, $id)
{
    $request = findExchangeRequest($pdo, $currentUser, $id);
    $input = readJsonInput();
    requireFields($input, ["status"]);

    $newStatus = stringValue($input, "status");
    $allowedStatuses = ["accepted", "rejected", "in_progress", "cancelled"];

    if (!in_array($newStatus, $allowedStatuses, true)) {
        jsonResponse(422, false, "Status hanya boleh accepted, rejected, in_progress, atau cancelled");
    }

    if (in_array($newStatus, ["accepted", "rejected"], true) && (int) $request["to_user_id"] !== $currentUser["id"]) {
        jsonResponse(403, false, "Hanya penerima request yang boleh accept atau reject");
    }

    if ($request["status"] !== "pending" && in_array($newStatus, ["accepted", "rejected"], true)) {
        jsonResponse(422, false, "Request yang sudah diproses tidak bisa accept atau reject ulang");
    }

    if ($newStatus === "in_progress" && !in_array($request["status"], ["accepted", "in_progress"], true)) {
        jsonResponse(422, false, "Exchange harus accepted sebelum menjadi in_progress");
    }

    $stmt = $pdo->prepare("UPDATE exchange_requests SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);

    jsonResponse(200, true, "Status exchange request berhasil diperbarui", [
        "id" => $id,
        "status" => $newStatus
    ]);
}

function completeExchange($pdo, $currentUser, $id)
{
    $request = findExchangeRequest($pdo, $currentUser, $id);

    if (!in_array($request["status"], ["accepted", "in_progress", "completed"], true)) {
        jsonResponse(422, false, "Exchange belum bisa dikonfirmasi selesai");
    }

    $fromDone = (bool) $request["completed_by_from_user"];
    $toDone = (bool) $request["completed_by_to_user"];

    if ((int) $request["from_user_id"] === $currentUser["id"]) {
        $fromDone = true;
    }

    if ((int) $request["to_user_id"] === $currentUser["id"]) {
        $toDone = true;
    }

    $status = $fromDone && $toDone ? "completed" : "in_progress";

    $stmt = $pdo->prepare("
        UPDATE exchange_requests
        SET completed_by_from_user = ?, completed_by_to_user = ?, status = ?
        WHERE id = ?
    ");
    $stmt->execute([$fromDone ? 1 : 0, $toDone ? 1 : 0, $status, $id]);

    $message = $status === "completed"
        ? "Exchange selesai"
        : "Menunggu konfirmasi dari user lain";

    jsonResponse(200, true, $message, [
        "id" => $id,
        "completed_by_me" => true,
        "completed_by_partner" => (int) $request["from_user_id"] === $currentUser["id"] ? $toDone : $fromDone,
        "status" => $status
    ]);
}

function findExchangeRequest($pdo, $currentUser, $id)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM exchange_requests
        WHERE id = ?
        AND (from_user_id = ? OR to_user_id = ?)
    ");
    $stmt->execute([$id, $currentUser["id"], $currentUser["id"]]);
    $request = $stmt->fetch();

    if (!$request) {
        jsonResponse(404, false, "Exchange request tidak ditemukan atau bukan milik Anda");
    }

    return $request;
}

function assertUserExists($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "User tujuan tidak ditemukan");
    }
}

function assertRowExists($pdo, $table, $id, $label)
{
    $allowedTables = ["offers", "needs"];

    if (!in_array($table, $allowedTables, true)) {
        jsonResponse(500, false, "Konfigurasi validasi tidak valid");
    }

    $stmt = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "$label tidak ditemukan");
    }
}
