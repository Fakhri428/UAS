<?php

require_once __DIR__ . "/auth.php";

allowCors("GET");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$currentUser = requireBearerToken($pdo);

jsonResponse(200, true, "Data user login berhasil diambil", $currentUser);
