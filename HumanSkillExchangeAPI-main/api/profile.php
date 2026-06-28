<?php

require_once __DIR__ . "/auth.php";

allowCors("GET, POST, PUT");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getProfile($pdo, $currentUser);
        break;

    case "POST":
    case "PUT":
        saveProfile($pdo, $currentUser);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getProfile($pdo, $currentUser)
{
    $userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : $currentUser["id"];

    $stmt = $pdo->prepare("
        SELECT users.id AS user_id, users.name, users.email, users.role, plans.name AS plan_name,
               profiles.bio, profiles.location, profiles.work_mode, profiles.available_time,
               profiles.portfolio_url, profiles.social_url
        FROM users
        LEFT JOIN profiles ON profiles.user_id = users.id
        LEFT JOIN plans ON plans.id = users.plan_id
        WHERE users.id = ?
    ");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();

    if (!$profile) {
        jsonResponse(404, false, "Profil user tidak ditemukan");
    }

    $profile["user_id"] = (int) $profile["user_id"];

    jsonResponse(200, true, "Profil berhasil diambil", $profile);
}

function saveProfile($pdo, $currentUser)
{
    $input = readJsonInput();
    requireFields($input, ["bio", "location", "work_mode", "available_time"]);

    $bio = stringValue($input, "bio");
    $location = stringValue($input, "location");
    $workMode = stringValue($input, "work_mode");
    $availableTime = stringValue($input, "available_time");
    $portfolioUrl = nullableString($input, "portfolio_url");
    $socialUrl = nullableString($input, "social_url");

    $allowedModes = ["online", "offline", "hybrid"];

    if (!in_array($workMode, $allowedModes, true)) {
        jsonResponse(422, false, "work_mode hanya boleh online, offline, atau hybrid");
    }

    $stmt = $pdo->prepare("
        INSERT INTO profiles (user_id, bio, location, work_mode, available_time, portfolio_url, social_url)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            bio = VALUES(bio),
            location = VALUES(location),
            work_mode = VALUES(work_mode),
            available_time = VALUES(available_time),
            portfolio_url = VALUES(portfolio_url),
            social_url = VALUES(social_url)
    ");
    $stmt->execute([
        $currentUser["id"],
        $bio,
        $location,
        $workMode,
        $availableTime,
        $portfolioUrl,
        $socialUrl
    ]);

    jsonResponse(200, true, "Profil berhasil disimpan", [
        "user_id" => $currentUser["id"],
        "bio" => $bio,
        "location" => $location,
        "work_mode" => $workMode,
        "available_time" => $availableTime,
        "portfolio_url" => $portfolioUrl,
        "social_url" => $socialUrl
    ]);
}
