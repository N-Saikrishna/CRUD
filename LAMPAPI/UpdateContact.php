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

foreach (array('id', 'userId', 'firstName', 'lastName', 'phone', 'email') as $field) {
    if (!isset($in[$field])) {
        sendJson(array("error" => "Missing required field: " . $field));
    }
}

$token = isset($in["token"]) ? $in["token"] : "";
$expiresAt = isset($in["expiresAt"]) ? $in["expiresAt"] : "";

if (!verifySessionToken($token, $expiresAt)) {
    http_response_code(401);
    sendJson(array("error" => "Invalid or expired session token"));
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    sendJson(array("error" => "Database connection failed"));
}

$firstName = $in["firstName"];
$lastName  = $in["lastName"];
$phone     = $in["phone"];
$email     = $in["email"];
$id        = (int)$in["id"];
$userId    = (int)$in["userId"];

$stmt = $conn->prepare(
    "UPDATE Contacts SET FirstName=?, LastName=?, Phone=?, Email=?
     WHERE ID=? AND UserID=?"
);
$stmt->bind_param("ssssii", $firstName, $lastName, $phone, $email, $id, $userId);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    sendJson(array("error" => "Contact not found"));
}

$stmt->close();
$conn->close();
sendJson(array("error" => ""));
?>
