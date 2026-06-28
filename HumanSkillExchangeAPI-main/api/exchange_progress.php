<?php

require_once __DIR__ . "/auth.php";

allowCors("GET, POST, PUT, DELETE");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;
$exchangeRequestId = isset($_GET["exchange_request_id"]) ? (int) $_GET["exchange_request_id"] : null;

switch ($method) {
    case "GET":
        if (!$exchangeRequestId) {
            jsonResponse(400, false, "Parameter exchange_request_id wajib dikirim");
        }
        getProgressList($pdo, $currentUser, $exchangeRequestId);
        break;

    case "POST":
        if (!$exchangeRequestId) {
            jsonResponse(400, false, "Parameter exchange_request_id wajib dikirim");
        }
        createProgress($pdo, $currentUser, $exchangeRequestId);
        break;

    case "PUT":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        updateProgress($pdo, $currentUser, $id);
        break;

    case "DELETE":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        deleteProgress($pdo, $currentUser, $id);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getProgressList($pdo, $currentUser, $exchangeRequestId)
{
    assertExchangeParticipant($pdo, $currentUser, $exchangeRequestId);

    $stmt = $pdo->prepare("
        SELECT exchange_progress.*, users.name AS user_name
        FROM exchange_progress
        JOIN users ON users.id = exchange_progress.user_id
        WHERE exchange_progress.exchange_request_id = ?
        ORDER BY exchange_progress.id DESC
    ");
    $stmt->execute([$exchangeRequestId]);

    jsonResponse(200, true, "Progress exchange berhasil diambil", $stmt->fetchAll());
}

function createProgress($pdo, $currentUser, $exchangeRequestId)
{
    assertExchangeParticipant($pdo, $currentUser, $exchangeRequestId);

    $input = readJsonInput();
    requireFields($input, ["progress_note"]);

    $progressNote = stringValue($input, "progress_note");
    $fileUrl = nullableString($input, "file_url");

    $stmt = $pdo->prepare("
        INSERT INTO exchange_progress (exchange_request_id, user_id, progress_note, file_url)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $exchangeRequestId,
        $currentUser["id"],
        $progressNote,
        $fileUrl
    ]);

    jsonResponse(201, true, "Progress exchange berhasil ditambahkan", [
        "id" => (int) $pdo->lastInsertId(),
        "exchange_request_id" => $exchangeRequestId,
        "user_id" => $currentUser["id"],
        "progress_note" => $progressNote,
        "file_url" => $fileUrl
    ]);
}

function updateProgress($pdo, $currentUser, $id)
{
    assertProgressOwner($pdo, $currentUser, $id);

    $input = readJsonInput();
    requireFields($input, ["progress_note"]);

    $progressNote = stringValue($input, "progress_note");
    $fileUrl = nullableString($input, "file_url");

    $stmt = $pdo->prepare("
        UPDATE exchange_progress
        SET progress_note = ?, file_url = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$progressNote, $fileUrl, $id, $currentUser["id"]]);

    jsonResponse(200, true, "Progress exchange berhasil diperbarui", [
        "id" => $id,
        "progress_note" => $progressNote,
        "file_url" => $fileUrl
    ]);
}

function deleteProgress($pdo, $currentUser, $id)
{
    assertProgressOwner($pdo, $currentUser, $id);

    $stmt = $pdo->prepare("DELETE FROM exchange_progress WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUser["id"]]);

    jsonResponse(200, true, "Progress exchange berhasil dihapus", ["id" => $id]);
}

function assertExchangeParticipant($pdo, $currentUser, $exchangeRequestId)
{
    $stmt = $pdo->prepare("
        SELECT id
        FROM exchange_requests
        WHERE id = ?
        AND (from_user_id = ? OR to_user_id = ?)
    ");
    $stmt->execute([$exchangeRequestId, $currentUser["id"], $currentUser["id"]]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "Exchange request tidak ditemukan atau bukan milik Anda");
    }
}

function assertProgressOwner($pdo, $currentUser, $id)
{
    $stmt = $pdo->prepare("SELECT id FROM exchange_progress WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUser["id"]]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "Progress tidak ditemukan atau bukan milik Anda");
    }
}
