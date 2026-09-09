<?php

function sendHeaders(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit();
    }
}

function requirePost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Only POST is supported on this endpoint', 405);
    }
}

function sendResult(array $payload, int $status = 200): void
{
    if (!array_key_exists('error', $payload)) {
        $payload['error'] = '';
    }
    http_response_code($status);
    echo json_encode($payload);
    exit();
}

function sendError(string $message, int $status = 400): void
{
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit();
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        sendError('Request body is empty', 400);
    }

    $body = json_decode($raw, true);
    if (!is_array($body)) {
        sendError('Request body is not valid JSON', 400);
    }

    return $body;
}

function requireField(array $body, string $name): string
{
    if (!isset($body[$name]) || !is_scalar($body[$name])) {
        sendError("Missing required field: $name", 400);
    }

    $value = trim((string)$body[$name]);
    if ($value === '') {
        sendError("Field cannot be empty: $name", 400);
    }

    return $value;
}

function optionalField(array $body, string $name, string $default = ''): string
{
    if (!isset($body[$name]) || !is_scalar($body[$name])) {
        return $default;
    }

    return trim((string)$body[$name]);
}

function requireInt(array $body, string $name): int
{
    if (!isset($body[$name]) || !is_numeric($body[$name])) {
        sendError("Missing or non numeric field: $name", 400);
    }

    return (int)$body[$name];
}

function textLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function escapeLike(string $value): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
}

function issueSessionToken(): array
{
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    return ['token' => $token, 'expiresAt' => $expiresAt];
}

function verifySessionToken(string $token, string $expiresAt): bool
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return false;
    }

    $expiresDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $expiresAt);
    if ($expiresDate === false) {
        return false;
    }

    return $expiresDate > new DateTimeImmutable();
}

function validateContactFields(string $firstName, string $lastName, string $phone, string $email): void
{
    if ($firstName === '' && $lastName === '') {
        sendError('A contact needs a first name or a last name', 400);
    }
    if (textLength($firstName) > 50 || textLength($lastName) > 50) {
        sendError('Names must be 50 characters or fewer', 400);
    }
    if (textLength($phone) > 25) {
        sendError('Phone must be 25 characters or fewer', 400);
    }
    if (textLength($email) > 100) {
        sendError('Email must be 100 characters or fewer', 400);
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Email address is not valid', 400);
    }
}
