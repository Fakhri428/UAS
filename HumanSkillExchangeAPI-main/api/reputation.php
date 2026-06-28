<?php

require_once __DIR__ . "/auth.php";

allowCors("GET");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$currentUser = requireBearerToken($pdo);
$userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : $currentUser["id"];

$userStmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

if (!$user) {
    jsonResponse(404, false, "User tidak ditemukan");
}

$completedStmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM exchange_requests
    WHERE status IN ('completed', 'reviewed')
    AND (from_user_id = ? OR to_user_id = ?)
");
$completedStmt->execute([$userId, $userId]);
$completedExchange = (int) $completedStmt->fetch()["total"];

$reviewStmt = $pdo->prepare("
    SELECT COUNT(*) AS total_reviews, AVG(rating) AS average_rating
    FROM reviews
    WHERE reviewed_user_id = ?
");
$reviewStmt->execute([$userId]);
$reviewStats = $reviewStmt->fetch();

$skillStmt = $pdo->prepare("
    SELECT category, COUNT(*) AS total
    FROM skills
    WHERE user_id = ?
    GROUP BY category
    ORDER BY total DESC
    LIMIT 3
");
$skillStmt->execute([$userId]);

$totalReviews = (int) $reviewStats["total_reviews"];
$averageRating = $reviewStats["average_rating"] !== null ? round((float) $reviewStats["average_rating"], 2) : 0;
$reputationScore = min(100, ($completedExchange * 10) + ($averageRating * 10) + ($totalReviews * 3));

jsonResponse(200, true, "Reputasi user berhasil dihitung", [
    "user" => [
        "id" => (int) $user["id"],
        "name" => $user["name"],
        "email" => $user["email"]
    ],
    "completed_exchange" => $completedExchange,
    "total_reviews" => $totalReviews,
    "average_rating" => $averageRating,
    "top_skill_categories" => $skillStmt->fetchAll(),
    "reputation_score" => (int) round($reputationScore)
]);
