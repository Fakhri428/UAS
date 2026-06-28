<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/response.php";

allowCors("POST");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$input = readJsonInput();
requireFields($input, ["name", "email", "password"]);

$name = stringValue($input, "name");
$email = strtolower(stringValue($input, "email"));
$password = stringValue($input, "password");

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(422, false, "Format email tidak valid");
}

if (strlen($password) < 6) {
    jsonResponse(422, false, "Password minimal 6 karakter");
}

$token = bin2hex(random_bytes(32));
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, plan_id, token)
        VALUES (?, ?, ?, 'user', 1, ?)
    ");
    $stmt->execute([$name, $email, $passwordHash, $token]);

    jsonResponse(201, true, "Register berhasil", [
        "user" => [
            "id" => (int) $pdo->lastInsertId(),
            "name" => $name,
            "email" => $email,
            "role" => "user",
            "plan" => "Gratis"
        ],
        "token_type" => "Bearer",
        "access_token" => $token
    ]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        jsonResponse(409, false, "Email sudah digunakan");
    }

    jsonResponse(500, false, "Register gagal", $e->getMessage());
}
