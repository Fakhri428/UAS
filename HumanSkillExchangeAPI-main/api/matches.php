<?php

require_once __DIR__ . "/auth.php";

allowCors("GET");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$currentUser = requireBearerToken($pdo);
$offerId = isset($_GET["offer_id"]) ? (int) $_GET["offer_id"] : null;
$needId = isset($_GET["need_id"]) ? (int) $_GET["need_id"] : null;

if ($offerId) {
    getMatchesForOffer($pdo, $currentUser, $offerId);
}

if ($needId) {
    getMatchesForNeed($pdo, $currentUser, $needId);
}

getUserMatches($pdo, $currentUser);

function getMatchesForOffer($pdo, $currentUser, $offerId)
{
    $offer = getOwnedRow($pdo, "offers", $offerId, $currentUser["id"], "Offer");

    $stmt = $pdo->prepare("
        SELECT needs.*, users.name AS user_name, profiles.work_mode, profiles.location
        FROM needs
        JOIN users ON users.id = needs.user_id
        LEFT JOIN profiles ON profiles.user_id = users.id
        WHERE needs.user_id <> ?
        ORDER BY needs.id DESC
        LIMIT 50
    ");
    $stmt->execute([$currentUser["id"]]);
    $matches = [];

    foreach ($stmt->fetchAll() as $need) {
        $score = scoreOfferNeed($offer, $need, getAverageRating($pdo, (int) $need["user_id"]));

        if ($score <= 0) {
            continue;
        }

        $matches[] = [
            "user_id" => (int) $need["user_id"],
            "name" => $need["user_name"],
            "need_id" => (int) $need["id"],
            "need_title" => $need["title"],
            "work_mode" => $need["work_mode"],
            "location" => $need["location"],
            "match_score" => $score,
            "reason" => buildOfferNeedReason($offer, $need)
        ];
    }

    usort($matches, fn($a, $b) => $b["match_score"] <=> $a["match_score"]);

    jsonResponse(200, true, "Rekomendasi match untuk offer berhasil diambil", [
        "offer_id" => $offerId,
        "matches" => $matches
    ]);
}

function getMatchesForNeed($pdo, $currentUser, $needId)
{
    $need = getOwnedRow($pdo, "needs", $needId, $currentUser["id"], "Need");

    $stmt = $pdo->prepare("
        SELECT offers.*, users.name AS user_name, profiles.work_mode, profiles.location
        FROM offers
        JOIN users ON users.id = offers.user_id
        LEFT JOIN profiles ON profiles.user_id = users.id
        WHERE offers.user_id <> ?
        ORDER BY offers.id DESC
        LIMIT 50
    ");
    $stmt->execute([$currentUser["id"]]);
    $matches = [];

    foreach ($stmt->fetchAll() as $offer) {
        $score = scoreOfferNeed($offer, $need, getAverageRating($pdo, (int) $offer["user_id"]));

        if ($score <= 0) {
            continue;
        }

        $matches[] = [
            "user_id" => (int) $offer["user_id"],
            "name" => $offer["user_name"],
            "offer_id" => (int) $offer["id"],
            "offer_title" => $offer["title"],
            "type" => $offer["type"],
            "work_mode" => $offer["work_mode"],
            "location" => $offer["location"],
            "match_score" => $score,
            "reason" => buildOfferNeedReason($offer, $need)
        ];
    }

    usort($matches, fn($a, $b) => $b["match_score"] <=> $a["match_score"]);

    jsonResponse(200, true, "Rekomendasi match untuk need berhasil diambil", [
        "need_id" => $needId,
        "matches" => $matches
    ]);
}

function getUserMatches($pdo, $currentUser)
{
    $myOffers = getRows($pdo, "SELECT * FROM offers WHERE user_id = ?", [$currentUser["id"]]);
    $myNeeds = getRows($pdo, "SELECT * FROM needs WHERE user_id = ?", [$currentUser["id"]]);

    $users = getRows($pdo, "
        SELECT users.id, users.name, profiles.work_mode, profiles.location
        FROM users
        LEFT JOIN profiles ON profiles.user_id = users.id
        WHERE users.id <> ?
        ORDER BY users.id DESC
        LIMIT 50
    ", [$currentUser["id"]]);

    $matches = [];

    foreach ($users as $user) {
        $theirNeeds = getRows($pdo, "SELECT * FROM needs WHERE user_id = ?", [$user["id"]]);
        $theirOffers = getRows($pdo, "SELECT * FROM offers WHERE user_id = ?", [$user["id"]]);

        $bestMyOffer = bestOfferNeedScore($myOffers, $theirNeeds);
        $bestTheirOffer = bestOfferNeedScore($theirOffers, $myNeeds);

        if ($bestMyOffer["score"] <= 0 && $bestTheirOffer["score"] <= 0) {
            continue;
        }

        $ratingScore = min(10, (int) round(getAverageRating($pdo, (int) $user["id"]) * 2));
        $score = 0;

        if ($bestMyOffer["score"] > 0 && $bestTheirOffer["score"] > 0) {
            $score += 60;
        } else {
            $score += 35;
        }

        $score += min(20, (int) round(($bestMyOffer["score"] + $bestTheirOffer["score"]) / 8));
        $score += compatibleWorkMode($pdo, $currentUser["id"], (int) $user["id"]) ? 10 : 0;
        $score += $ratingScore;
        $score = min(100, $score);

        $matches[] = [
            "user_id" => (int) $user["id"],
            "name" => $user["name"],
            "work_mode" => $user["work_mode"],
            "location" => $user["location"],
            "match_score" => $score,
            "reason" => buildUserMatchReason($bestMyOffer, $bestTheirOffer),
            "suggested_offer_id" => $bestMyOffer["offer_id"],
            "suggested_need_id" => $bestTheirOffer["need_id"]
        ];
    }

    usort($matches, fn($a, $b) => $b["match_score"] <=> $a["match_score"]);

    jsonResponse(200, true, "Daftar match berhasil diambil", [
        "user_id" => $currentUser["id"],
        "matches" => $matches
    ]);
}

function scoreOfferNeed($offer, $need, $averageRating = 0)
{
    $score = 0;

    if (strtolower($offer["category"]) === strtolower($need["category"])) {
        $score += 40;
    }

    $offerText = implode(" ", [
        $offer["title"] ?? "",
        $offer["category"] ?? "",
        $offer["description"] ?? "",
        $offer["exchange_expectation"] ?? ""
    ]);

    $needText = implode(" ", [
        $need["title"] ?? "",
        $need["category"] ?? "",
        $need["description"] ?? "",
        $need["exchange_offer"] ?? ""
    ]);

    if (hasKeywordOverlap($offerText, $needText)) {
        $score += 45;
    }

    $score += min(10, (int) round($averageRating * 2));

    return min(100, $score);
}

function bestOfferNeedScore($offers, $needs)
{
    $best = [
        "score" => 0,
        "offer_id" => null,
        "offer_title" => null,
        "need_id" => null,
        "need_title" => null
    ];

    foreach ($offers as $offer) {
        foreach ($needs as $need) {
            $score = scoreOfferNeed($offer, $need);

            if ($score > $best["score"]) {
                $best = [
                    "score" => $score,
                    "offer_id" => (int) $offer["id"],
                    "offer_title" => $offer["title"],
                    "need_id" => (int) $need["id"],
                    "need_title" => $need["title"]
                ];
            }
        }
    }

    return $best;
}

function hasKeywordOverlap($firstText, $secondText)
{
    $firstTokens = textTokens($firstText);
    $secondTokens = textTokens($secondText);
    $intersection = array_intersect($firstTokens, $secondTokens);

    return count($intersection) > 0;
}

function textTokens($text)
{
    $normalized = strtolower(preg_replace("/[^a-z0-9]+/i", " ", $text));
    $tokens = preg_split("/\s+/", trim($normalized));

    return array_values(array_unique(array_filter($tokens, function ($token) {
        return strlen($token) >= 3;
    })));
}

function compatibleWorkMode($pdo, $firstUserId, $secondUserId)
{
    $profiles = getRows($pdo, "
        SELECT user_id, work_mode, location
        FROM profiles
        WHERE user_id IN (?, ?)
    ", [$firstUserId, $secondUserId]);

    if (count($profiles) < 2) {
        return false;
    }

    $first = $profiles[0];
    $second = $profiles[1];

    if ($first["work_mode"] === "online" || $second["work_mode"] === "online") {
        return true;
    }

    if ($first["work_mode"] === "hybrid" || $second["work_mode"] === "hybrid") {
        return true;
    }

    return strtolower((string) $first["location"]) === strtolower((string) $second["location"]);
}

function buildOfferNeedReason($offer, $need)
{
    if (strtolower($offer["category"]) === strtolower($need["category"])) {
        return "Kategori offer dan need sama, yaitu " . $offer["category"] . ".";
    }

    return "Terdapat kemiripan kata kunci antara offer dan need.";
}

function buildUserMatchReason($bestMyOffer, $bestTheirOffer)
{
    if ($bestMyOffer["score"] > 0 && $bestTheirOffer["score"] > 0) {
        return "Ada potensi match dua arah: offer Anda cocok dengan need mereka, dan offer mereka cocok dengan need Anda.";
    }

    if ($bestMyOffer["score"] > 0) {
        return "Offer Anda berpotensi membantu need user tersebut.";
    }

    return "Offer user tersebut berpotensi membantu need Anda.";
}

function getOwnedRow($pdo, $table, $id, $userId, $label)
{
    $allowedTables = ["offers", "needs"];

    if (!in_array($table, $allowedTables, true)) {
        jsonResponse(500, false, "Konfigurasi matching tidak valid");
    }

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        jsonResponse(404, false, "$label tidak ditemukan atau bukan milik Anda");
    }

    return $row;
}

function getRows($pdo, $sql, $params = [])
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAverageRating($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT AVG(rating) AS average_rating FROM reviews WHERE reviewed_user_id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    return $row && $row["average_rating"] !== null ? (float) $row["average_rating"] : 0;
}
