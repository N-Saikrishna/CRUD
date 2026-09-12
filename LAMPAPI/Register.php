<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/db.php';

function registerUser(array $body): array
{
    $firstName = requireField($body, 'firstName');
    $lastName = requireField($body, 'lastName');
    $login = requireField($body, 'login');
    $password = requireField($body, 'password');

    if (textLength($login) > 50) {
        sendError('Login must be 50 characters or fewer', 400);
    }

    if (textLength($password) < 6) {
        sendError('Password must be at least 6 characters', 400);
    }

    $conn = getConnection();
    $existing = $conn->prepare('SELECT ID FROM Users WHERE Login = ? LIMIT 1');
    $existing->bind_param('s', $login);
    $existing->execute();
    $existingResult = $existing->get_result();
    $alreadyExists = $existingResult->fetch_assoc();
    $existing->close();

    if ($alreadyExists) {
        $conn->close();
        sendError('A user with that login already exists', 409);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $firstName, $lastName, $login, $hashedPassword);
    $stmt->execute();
    $userId = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    return [
        'id' => (int)$userId,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'error' => '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sendHeaders();
    $body = readJsonBody();
    $result = registerUser($body);
    sendResult($result, 201);
}

sendHeaders();
sendError('Only POST is supported on this endpoint', 405);
