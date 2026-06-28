<?php

require_once __DIR__ . "/auth.php";

allowCors("GET, POST, PATCH");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        getMyPlan($pdo, $currentUser);
        break;

    case "POST":
        subscribePlan($pdo, $currentUser);
        break;

    case "PATCH":
        cancelSubscription($pdo, $currentUser);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getMyPlan($pdo, $currentUser)
{
    $stmt = $pdo->prepare("
        SELECT plans.*
        FROM users
        JOIN plans ON plans.id = users.plan_id
        WHERE users.id = ?
    ");
    $stmt->execute([$currentUser["id"]]);
    $plan = $stmt->fetch();

    jsonResponse(200, true, "Paket aktif berhasil diambil", $plan);
}

function subscribePlan($pdo, $currentUser)
{
    $input = readJsonInput();
    requireFields($input, ["plan_id"]);

    $planId = (int) $input["plan_id"];
    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$planId]);
    $plan = $stmt->fetch();

    if (!$plan) {
        jsonResponse(404, false, "Paket tidak ditemukan");
    }

    $update = $pdo->prepare("UPDATE users SET plan_id = ? WHERE id = ?");
    $update->execute([$planId, $currentUser["id"]]);

    jsonResponse(200, true, "Paket berhasil diaktifkan", $plan);
}

function cancelSubscription($pdo, $currentUser)
{
    $update = $pdo->prepare("UPDATE users SET plan_id = 1 WHERE id = ?");
    $update->execute([$currentUser["id"]]);

    $stmt = $pdo->query("SELECT * FROM plans WHERE id = 1");
    $plan = $stmt->fetch();

    jsonResponse(200, true, "Subscription dibatalkan. Paket kembali ke Gratis", $plan);
}
