<?php

require_once __DIR__ . "/response.php";

function enforcePlanLimit($pdo, $user, $table, $limitColumn, $featureName)
{
    $allowedTables = ["skills", "needs", "offers"];
    $allowedColumns = ["max_skills", "max_needs", "max_offers"];

    if (!in_array($table, $allowedTables, true) || !in_array($limitColumn, $allowedColumns, true)) {
        jsonResponse(500, false, "Konfigurasi limit paket tidak valid");
    }

    $stmt = $pdo->prepare("SELECT $limitColumn AS max_limit FROM plans WHERE id = ?");
    $stmt->execute([$user["plan_id"]]);
    $plan = $stmt->fetch();

    if (!$plan || $plan["max_limit"] === null) {
        return;
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM $table WHERE user_id = ?");
    $countStmt->execute([$user["id"]]);
    $total = (int) $countStmt->fetch()["total"];

    if ($total >= (int) $plan["max_limit"]) {
        jsonResponse(403, false, "Batas $featureName untuk paket Anda sudah tercapai");
    }
}

function enforceMonthlyExchangeLimit($pdo, $user)
{
    $stmt = $pdo->prepare("SELECT max_exchange_requests FROM plans WHERE id = ?");
    $stmt->execute([$user["plan_id"]]);
    $plan = $stmt->fetch();

    if (!$plan || $plan["max_exchange_requests"] === null) {
        return;
    }

    $countStmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM exchange_requests
        WHERE from_user_id = ?
        AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    ");
    $countStmt->execute([$user["id"]]);
    $total = (int) $countStmt->fetch()["total"];

    if ($total >= (int) $plan["max_exchange_requests"]) {
        jsonResponse(403, false, "Batas exchange request bulanan untuk paket Anda sudah tercapai");
    }
}
