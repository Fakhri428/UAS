<?php

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/response.php";

allowCors("GET");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, false, "Method tidak diizinkan");
}

$stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
$plans = $stmt->fetchAll();

jsonResponse(200, true, "Data paket langganan berhasil diambil", $plans);
