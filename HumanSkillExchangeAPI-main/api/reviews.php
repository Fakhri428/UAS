<?php

require_once __DIR__ . "/auth.php";

allowCors("GET, POST");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getReviewsByUser($pdo);
        break;

    case "POST":
        createReview($pdo, $currentUser);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getReviewsByUser($pdo)
{
    $userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : null;

    if (!$userId) {
        jsonResponse(400, false, "Parameter user_id wajib dikirim");
    }

    $stmt = $pdo->prepare("
        SELECT reviews.*, reviewer.name AS reviewer_name
        FROM reviews
        JOIN users AS reviewer ON reviewer.id = reviews.reviewer_id
        WHERE reviews.reviewed_user_id = ?
        ORDER BY reviews.id DESC
    ");
    $stmt->execute([$userId]);

    jsonResponse(200, true, "Review user berhasil diambil", $stmt->fetchAll());
}

function createReview($pdo, $currentUser)
{
    $input = readJsonInput();
    requireFields($input, ["exchange_request_id", "reviewed_user_id", "rating", "comment"]);

    $exchangeRequestId = (int) $input["exchange_request_id"];
    $reviewedUserId = (int) $input["reviewed_user_id"];
    $rating = (int) $input["rating"];
    $comment = stringValue($input, "comment");

    if ($rating < 1 || $rating > 5) {
        jsonResponse(422, false, "Rating hanya boleh 1 sampai 5");
    }

    $request = findCompletedExchange($pdo, $currentUser, $exchangeRequestId);

    $participants = [(int) $request["from_user_id"], (int) $request["to_user_id"]];

    if (!in_array($reviewedUserId, $participants, true) || $reviewedUserId === $currentUser["id"]) {
        jsonResponse(422, false, "User yang direview harus partner exchange");
    }

    $duplicate = $pdo->prepare("
        SELECT id
        FROM reviews
        WHERE exchange_request_id = ?
        AND reviewer_id = ?
        AND reviewed_user_id = ?
    ");
    $duplicate->execute([$exchangeRequestId, $currentUser["id"], $reviewedUserId]);

    if ($duplicate->fetch()) {
        jsonResponse(409, false, "Anda sudah memberi review untuk exchange ini");
    }

    $stmt = $pdo->prepare("
        INSERT INTO reviews (exchange_request_id, reviewer_id, reviewed_user_id, rating, comment)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $exchangeRequestId,
        $currentUser["id"],
        $reviewedUserId,
        $rating,
        $comment
    ]);

    markReviewedIfComplete($pdo, $exchangeRequestId);

    jsonResponse(201, true, "Review berhasil dikirim", [
        "id" => (int) $pdo->lastInsertId(),
        "exchange_request_id" => $exchangeRequestId,
        "reviewer_id" => $currentUser["id"],
        "reviewed_user_id" => $reviewedUserId,
        "rating" => $rating,
        "comment" => $comment
    ]);
}

function findCompletedExchange($pdo, $currentUser, $exchangeRequestId)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM exchange_requests
        WHERE id = ?
        AND status IN ('completed', 'reviewed')
        AND (from_user_id = ? OR to_user_id = ?)
    ");
    $stmt->execute([$exchangeRequestId, $currentUser["id"], $currentUser["id"]]);
    $request = $stmt->fetch();

    if (!$request) {
        jsonResponse(404, false, "Exchange completed tidak ditemukan atau bukan milik Anda");
    }

    return $request;
}

function markReviewedIfComplete($pdo, $exchangeRequestId)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM reviews
        WHERE exchange_request_id = ?
    ");
    $stmt->execute([$exchangeRequestId]);
    $total = (int) $stmt->fetch()["total"];

    if ($total >= 2) {
        $update = $pdo->prepare("UPDATE exchange_requests SET status = 'reviewed' WHERE id = ?");
        $update->execute([$exchangeRequestId]);
    }
}
