<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/response.php";

allowCors("POST");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$input = readJsonInput();
requireFields($input, ["email", "password"]);

$email = strtolower(stringValue($input, "email"));
$password = stringValue($input, "password");

$stmt = $pdo->prepare("
    SELECT users.id, users.name, users.email, users.password, users.role, users.token, plans.name AS plan_name
    FROM users
    LEFT JOIN plans ON plans.id = users.plan_id
    WHERE users.email = ?
");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password"])) {
    jsonResponse(401, false, "Email atau password salah");
}

if (!$user["token"]) {
    $user["token"] = bin2hex(random_bytes(32));
    $update = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
    $update->execute([$user["token"], $user["id"]]);
}

jsonResponse(200, true, "Login berhasil", [
    "user" => [
        "id" => (int) $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "role" => $user["role"],
        "plan" => $user["plan_name"]
    ],
    "token_type" => "Bearer",
    "access_token" => $user["token"]
]);
