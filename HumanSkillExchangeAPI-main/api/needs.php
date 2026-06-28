<?php

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/plan_limits.php";

allowCors("GET, POST, PUT, DELETE");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

switch ($method) {
    case "GET":
        $id ? getNeedById($pdo, $id) : getNeeds($pdo, $currentUser);
        break;

    case "POST":
        createNeed($pdo, $currentUser);
        break;

    case "PUT":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        updateNeed($pdo, $currentUser, $id);
        break;

    case "DELETE":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        deleteNeed($pdo, $currentUser, $id);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getNeeds($pdo, $currentUser)
{
    $userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : $currentUser["id"];
    $search = trim((string) ($_GET["search"] ?? ""));

    $params = [$userId];
    $sql = "SELECT * FROM needs WHERE user_id = ?";

    if ($search !== "") {
        $sql .= " AND (title LIKE ? OR category LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    jsonResponse(200, true, "Data need berhasil diambil", $stmt->fetchAll());
}

function getNeedById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM needs WHERE id = ?");
    $stmt->execute([$id]);
    $need = $stmt->fetch();

    if (!$need) {
        jsonResponse(404, false, "Need tidak ditemukan");
    }

    jsonResponse(200, true, "Detail need berhasil diambil", $need);
}

function createNeed($pdo, $currentUser)
{
    enforcePlanLimit($pdo, $currentUser, "needs", "max_needs", "need");

    $input = readJsonInput();
    $data = validateNeedInput($input);

    $stmt = $pdo->prepare("
        INSERT INTO needs (user_id, title, category, description, exchange_offer)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $currentUser["id"],
        $data["title"],
        $data["category"],
        $data["description"],
        $data["exchange_offer"]
    ]);

    $data["id"] = (int) $pdo->lastInsertId();
    $data["user_id"] = $currentUser["id"];

    jsonResponse(201, true, "Need berhasil ditambahkan", $data);
}

function updateNeed($pdo, $currentUser, $id)
{
    ensureNeedOwner($pdo, $id, $currentUser["id"]);

    $input = readJsonInput();
    $data = validateNeedInput($input);

    $stmt = $pdo->prepare("
        UPDATE needs
        SET title = ?, category = ?, description = ?, exchange_offer = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $data["title"],
        $data["category"],
        $data["description"],
        $data["exchange_offer"],
        $id,
        $currentUser["id"]
    ]);

    $data["id"] = $id;
    $data["user_id"] = $currentUser["id"];

    jsonResponse(200, true, "Need berhasil diperbarui", $data);
}

function deleteNeed($pdo, $currentUser, $id)
{
    ensureNeedOwner($pdo, $id, $currentUser["id"]);

    $stmt = $pdo->prepare("DELETE FROM needs WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUser["id"]]);

    jsonResponse(200, true, "Need berhasil dihapus", ["id" => $id]);
}

function validateNeedInput($input)
{
    requireFields($input, ["title", "category", "description", "exchange_offer"]);

    return [
        "title" => stringValue($input, "title"),
        "category" => stringValue($input, "category"),
        "description" => stringValue($input, "description"),
        "exchange_offer" => stringValue($input, "exchange_offer")
    ];
}

function ensureNeedOwner($pdo, $id, $userId)
{
    $stmt = $pdo->prepare("SELECT id FROM needs WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "Need tidak ditemukan atau bukan milik Anda");
    }
}
