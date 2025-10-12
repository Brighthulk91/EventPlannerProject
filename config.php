<?php
// config.php - database connection
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'event_planner';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
if (!defined('BASE_URL')) {
    // match your XAMPP folder path
    define('BASE_URL', 'http://localhost/event-planner');
}
?>
