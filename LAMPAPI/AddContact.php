<?php
require_once 'config.php';
require_once __DIR__ . '/common.php';

$in = json_decode(file_get_contents('php://input'), true);

function sendJson($obj)
{
    header('Content-Type: application/json');
    echo json_encode($obj);
    exit();
}

foreach (array('firstName', 'lastName', 'phone', 'email') as $field) {
    if (!isset($in[$field])) {
        sendJson(array("error" => "Missing required field: " . $field));
    }
}

$token = isset($in["token"]) ? $in["token"] : "";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    sendJson(array("error" => "Database connection failed"));
}

// The contact is tagged with the user the session token belongs to, never
// with a userId the browser sends, so nobody can file contacts under
// someone else's account.
$userId = resolveSessionUserId($conn, $token);

$firstName = trim($in["firstName"]);
$lastName  = trim($in["lastName"]);
$phone     = trim($in["phone"]);
$email     = trim($in["email"]);

validateContactFields($firstName, $lastName, $phone, $email);

$stmt = $conn->prepare(
    "INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("issss", $userId, $firstName, $lastName, $phone, $email);
$stmt->execute();

$newId = $conn->insert_id;

$stmt->close();
$conn->close();
sendJson(array("id" => (int)$newId, "error" => ""));
?>
