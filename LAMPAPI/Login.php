<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/db.php';

function loginUser(array $body): array
{
    $login = requireField($body, 'login');
    $password = requireField($body, 'password');

    $conn = getConnection();
    $stmt = $conn->prepare('SELECT ID, FirstName, LastName, Password FROM Users WHERE Login = ? LIMIT 1');
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $conn->close();
        sendError('Invalid login credentials', 401);
    }

    if (!password_verify($password, $user['Password'])) {
        $conn->close();
        sendError('Invalid login credentials', 401);
    }

    $issued = issueSessionToken();
    $token = $issued['token'];
    $expiresAt = $issued['expiresAt'];

    $sessionStmt = $conn->prepare('INSERT INTO Sessions (Token, UserID, ExpiresAt) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ExpiresAt = VALUES(ExpiresAt)');
    $sessionStmt->bind_param('sis', $token, $user['ID'], $expiresAt);
    $sessionStmt->execute();
    $sessionStmt->close();
    $conn->close();

    return [
        'id' => (int)$user['ID'],
        'firstName' => $user['FirstName'],
        'lastName' => $user['LastName'],
        'token' => $token,
        'expiresAt' => $expiresAt,
        'error' => '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendHeaders();
    $body = readJsonBody();
    $result = loginUser($body);
    sendResult($result, 200);
}

sendHeaders();
sendError('Only POST is supported on this endpoint', 405);

