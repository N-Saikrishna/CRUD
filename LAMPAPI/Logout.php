<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/db.php';

function logoutUser(array $body): array
{
    $token = requireField($body, 'token');

    $conn = getConnection();
    $stmt = $conn->prepare('SELECT UserID, ExpiresAt FROM Sessions WHERE Token = ? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();

    if (!$session) {
        $conn->close();
        sendError('Invalid session token', 401);
    }

    $expiresAt = $session['ExpiresAt'];
    $expiresDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $expiresAt);
    if ($expiresDate === false || $expiresDate <= new DateTimeImmutable()) {
        $conn->close();
        sendError('Session token has expired', 401);
    }

    $deleteStmt = $conn->prepare('DELETE FROM Sessions WHERE Token = ?');
    $deleteStmt->bind_param('s', $token);
    $deleteStmt->execute();
    $deleteStmt->close();
    $conn->close();

    return ['error' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendHeaders();
    $body = readJsonBody();
    $result = logoutUser($body);
    sendResult($result, 200);
}

sendHeaders();
sendError('Only POST is supported on this endpoint', 405);
