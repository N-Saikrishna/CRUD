<?php
require_once __DIR__ . '/config.php';

function getConnection(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
        return $conn;
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        $message = DEBUG_ERRORS ? $e->getMessage() : 'Database is unavailable';
        echo json_encode(['error' => $message]);
        exit();
    }
}
