<?php

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/plan_limits.php";

allowCors("GET, POST, PUT, DELETE");

$currentUser = requireBearerToken($pdo);
$method = $_SERVER["REQUEST_METHOD"];
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

switch ($method) {
    case "GET":
        $id ? getSkillById($pdo, $id) : getSkills($pdo, $currentUser);
        break;

    case "POST":
        createSkill($pdo, $currentUser);
        break;

    case "PUT":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        updateSkill($pdo, $currentUser, $id);
        break;

    case "DELETE":
        if (!$id) {
            jsonResponse(400, false, "Parameter id wajib dikirim");
        }
        deleteSkill($pdo, $currentUser, $id);
        break;

    default:
        jsonResponse(405, false, "Method tidak diizinkan");
}

function getSkills($pdo, $currentUser)
{
    $userId = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : $currentUser["id"];
    $search = trim((string) ($_GET["search"] ?? ""));

    $params = [$userId];
    $sql = "SELECT * FROM skills WHERE user_id = ?";

    if ($search !== "") {
        $sql .= " AND (name LIKE ? OR category LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    jsonResponse(200, true, "Data skill berhasil diambil", $stmt->fetchAll());
}

function getSkillById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    $skill = $stmt->fetch();

    if (!$skill) {
        jsonResponse(404, false, "Skill tidak ditemukan");
    }

    jsonResponse(200, true, "Detail skill berhasil diambil", $skill);
}

function createSkill($pdo, $currentUser)
{
    enforcePlanLimit($pdo, $currentUser, "skills", "max_skills", "skill");

    $input = readJsonInput();
    $data = validateSkillInput($input);

    $stmt = $pdo->prepare("
        INSERT INTO skills (user_id, name, category, level)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $currentUser["id"],
        $data["name"],
        $data["category"],
        $data["level"]
    ]);

    $data["id"] = (int) $pdo->lastInsertId();
    $data["user_id"] = $currentUser["id"];

    jsonResponse(201, true, "Skill berhasil ditambahkan", $data);
}

function updateSkill($pdo, $currentUser, $id)
{
    ensureOwner($pdo, "skills", $id, $currentUser["id"], "Skill");

    $input = readJsonInput();
    $data = validateSkillInput($input);

    $stmt = $pdo->prepare("
        UPDATE skills
        SET name = ?, category = ?, level = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
        $data["name"],
        $data["category"],
        $data["level"],
        $id,
        $currentUser["id"]
    ]);

    $data["id"] = $id;
    $data["user_id"] = $currentUser["id"];

    jsonResponse(200, true, "Skill berhasil diperbarui", $data);
}

function deleteSkill($pdo, $currentUser, $id)
{
    ensureOwner($pdo, "skills", $id, $currentUser["id"], "Skill");

    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $currentUser["id"]]);

    jsonResponse(200, true, "Skill berhasil dihapus", ["id" => $id]);
}

function validateSkillInput($input)
{
    requireFields($input, ["name", "category", "level"]);

    $level = stringValue($input, "level");
    $allowedLevels = ["beginner", "intermediate", "advanced"];

    if (!in_array($level, $allowedLevels, true)) {
        jsonResponse(422, false, "level hanya boleh beginner, intermediate, atau advanced");
    }

    return [
        "name" => stringValue($input, "name"),
        "category" => stringValue($input, "category"),
        "level" => $level
    ];
}

function ensureOwner($pdo, $table, $id, $userId, $label)
{
    $allowedTables = ["skills", "needs", "offers"];

    if (!in_array($table, $allowedTables, true)) {
        jsonResponse(500, false, "Konfigurasi ownership tidak valid");
    }

    $stmt = $pdo->prepare("SELECT id FROM $table WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    if (!$stmt->fetch()) {
        jsonResponse(404, false, "$label tidak ditemukan atau bukan milik Anda");
    }
}
