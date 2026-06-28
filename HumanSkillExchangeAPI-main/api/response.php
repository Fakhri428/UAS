<?php

function jsonResponse($statusCode, $status, $message, $data = null)
{
    http_response_code($statusCode);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ], JSON_PRETTY_PRINT);

    exit;
}

function allowCors($methods)
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: " . $methods . ", OPTIONS");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        exit;
    }
}

function readJsonInput()
{
    $raw = file_get_contents("php://input");

    if (trim($raw) === "") {
        return [];
    }

    $input = json_decode($raw, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        jsonResponse(400, false, "Format JSON tidak valid");
    }

    return $input;
}

function requireFields($input, $fields)
{
    $missing = [];

    foreach ($fields as $field) {
        if (!isset($input[$field]) || trim((string) $input[$field]) === "") {
            $missing[] = $field;
        }
    }

    if ($missing) {
        jsonResponse(422, false, "Field wajib belum lengkap: " . implode(", ", $missing));
    }
}

function stringValue($input, $field)
{
    return trim((string) ($input[$field] ?? ""));
}

function nullableString($input, $field)
{
    $value = stringValue($input, $field);
    return $value === "" ? null : $value;
}
