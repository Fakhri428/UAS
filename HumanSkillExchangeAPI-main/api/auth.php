<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/response.php";

function getAuthorizationHeader()
{
    $headers = null;

    if (isset($_SERVER["Authorization"])) {
        $headers = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists("apache_request_headers")) {
        $requestHeaders = apache_request_headers();

        if (isset($requestHeaders["Authorization"])) {
            $headers = trim($requestHeaders["Authorization"]);
        } elseif (isset($requestHeaders["authorization"])) {
            $headers = trim($requestHeaders["authorization"]);
        }
    }

    return $headers;
}

function requireBearerToken($pdo)
{
    $authHeader = getAuthorizationHeader();

    if (!$authHeader) {
        jsonResponse(401, false, "Authorization header belum dikirim");
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        jsonResponse(401, false, "Format token salah. Gunakan: Bearer token");
    }

    $token = $matches[1];

    $stmt = $pdo->prepare("
        SELECT users.id, users.name, users.email, users.role, users.plan_id, plans.name AS plan_name
        FROM users
        LEFT JOIN plans ON plans.id = users.plan_id
        WHERE users.token = ?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(401, false, "Token tidak valid");
    }

    return [
        "id" => (int) $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "role" => $user["role"],
        "plan_id" => $user["plan_id"] ? (int) $user["plan_id"] : null,
        "plan_name" => $user["plan_name"]
    ];
}

function requireAdmin($user)
{
    if (($user["role"] ?? "") !== "admin") {
        jsonResponse(403, false, "Endpoint ini hanya dapat diakses admin");
    }
}
