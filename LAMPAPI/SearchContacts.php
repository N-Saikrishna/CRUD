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

$token = isset($in["token"]) ? $in["token"] : "";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    sendJson(array("error" => "Database connection failed"));
}

$userId = resolveSessionUserId($conn, $token);

// An empty search returns everything, so the page has data when it first
// loads instead of a blank table.
$search = isset($in["search"]) ? trim($in["search"]) : "";

// escapeLike neutralises % and _ so a user searching for those characters
// does not accidentally match every row.
$like = "%" . escapeLike($search) . "%";

// Paging happens in SQL, not in the browser. The assignment says assume
// 10,000 contacts, so the client never receives more than one page.
$pageSize = 50;
$page     = isset($in["page"]) ? max(1, (int)$in["page"]) : 1;
$offset   = ($page - 1) * $pageSize;

$countStmt = $conn->prepare(
    "SELECT COUNT(*) FROM Contacts
     WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ?)"
);
$countStmt->bind_param("iss", $userId, $like, $like);
$countStmt->execute();
$countStmt->bind_result($total);
$countStmt->fetch();
$countStmt->close();

// The collation is utf8mb4_unicode_ci, which is case insensitive, so
// searching "jo" matches John, Jones and Jobs without any extra work.
$stmt = $conn->prepare(
    "SELECT ID, FirstName, LastName, Phone, Email, DateCreated
     FROM Contacts
     WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ?)
     ORDER BY LastName, FirstName
     LIMIT ? OFFSET ?"
);
$stmt->bind_param("issii", $userId, $like, $like, $pageSize, $offset);
$stmt->execute();
$result = $stmt->get_result();

$contacts = array();
while ($row = $result->fetch_assoc()) {
    $contacts[] = array(
        "id"          => (int)$row["ID"],
        "firstName"   => $row["FirstName"],
        "lastName"    => $row["LastName"],
        "phone"       => $row["Phone"],
        "email"       => $row["Email"],
        "dateCreated" => $row["DateCreated"]
    );
}

$stmt->close();
$conn->close();

sendJson(array(
    "results"  => $contacts,
    "page"     => $page,
    "pageSize" => $pageSize,
    "total"    => (int)$total,
    "error"    => ""
));
?>
