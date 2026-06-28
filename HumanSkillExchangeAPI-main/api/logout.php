<?php

require_once __DIR__ . "/auth.php";

allowCors("POST");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$currentUser = requireBearerToken($pdo);

$stmt = $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?");
$stmt->execute([$currentUser["id"]]);

jsonResponse(200, true, "Logout berhasil");
