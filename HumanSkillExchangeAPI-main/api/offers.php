<?php

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/plan_limits.php";

allowCors("GET, POST, PUT, DELETE");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

switch ($method) {
    case "GET":
        $id ? getOfferById($pdo, $id) : getOffers($pdo, $currentUser);
        break;

    case "POST":
        createOffer($pdo, $currentUser);
        break;

    case "PUT":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        updateOffer($pdo, $currentUser, $id);
        break;

    case "DELETE":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        deleteOffer($pdo, $currentUser, $id);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getOffers($pdo, $currentUser)
{
    $userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : $currentUser["id"];
    $search = trim((string) ($_GET["search"] ?? ""));

    $params = [$userId];
    $sql = "SELECT * FROM offers WHERE user_id = ?";

    if ($search !== "") {
        $sql .= " AND (title LIKE ? OR category LIKE ? OR description LIKE ? OR exchange_expectation LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    jsonResponse(200, true, "Data offer berhasil diambil", $stmt->fetchAll());
}

function getOfferById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->execute([$id]);
    $offer = $stmt->fetch();

    if (!$offer) {
        jsonResponse(404, false, "Offer tidak ditemukan");
    }

    jsonResponse(200, true, "Detail offer berhasil diambil", $offer);
}

function createOffer($pdo, $currentUser)
{
    enforcePlanLimit($pdo, $currentUser, "offers", "max_offers", "offer");

    $input = readJsonInput();
    $data = validateOfferInput($input);

    $stmt = $pdo->prepare("
        INSERT INTO offers (user_id, title, type, category, description, exchange_expectation, available_duration)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $currentUser["id"],
        $data["title"],
        $data["type"],
        $data["category"],
        $data["description"],
        $data["exchange_expectation"],
        $data["available_duration"]
    ]);

    $data["id"] = (int) $pdo->lastInsertId();
    $data["user_id"] = $currentUser["id"];

    jsonResponse(201, true, "Offer berhasil dibuat", $data);
}

function updateOffer($pdo, $currentUser, $id)
{
    ensureOfferOwner($pdo, $id, $currentUser["id"]);

    $input = readJsonInput();
    $data = validateOfferInput($input);

    $stmt = $pdo->prepare("
        UPDATE offers
        SET title = ?, type = ?, category = ?, description = ?, exchange_expectation = ?, available_duration = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $data["title"],
        $data["type"],
        $data["category"],
        $data["description"],
        $data["exchange_expectation"],
        $data["available_duration"],
        $id,
        $currentUser["id"]
    ]);

    $data["id"] = $id;
    $data["user_id"] = $currentUser["id"];

    jsonResponse(200, true, "Offer berhasil diperbarui", $data);
}

function deleteOffer($pdo, $currentUser, $id)
{
    ensureOfferOwner($pdo, $id, $currentUser["id"]);

    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUser["id"]]);

    jsonResponse(200, true, "Offer berhasil dihapus", ["id" => $id]);
}

function validateOfferInput($input)
{
    requireFields($input, ["title", "type", "category", "description", "exchange_expectation"]);

    $type = stringValue($input, "type");
    $allowedTypes = ["skill", "time", "experience", "mentoring", "project", "collaboration"];

    if (!in_array($type, $allowedTypes, true)) {
        jsonResponse(422, false, "type exchange tidak valid");
    }

    return [
        "title" => stringValue($input, "title"),
        "type" => $type,
        "category" => stringValue($input, "category"),
        "description" => stringValue($input, "description"),
        "exchange_expectation" => stringValue($input, "exchange_expectation"),
        "available_duration" => nullableString($input, "available_duration")
    ];
}

function ensureOfferOwner($pdo, $id, $userId)
{
    $stmt = $pdo->prepare("SELECT id FROM offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "Offer tidak ditemukan atau bukan milik Anda");
    }
}
