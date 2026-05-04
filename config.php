<?php
$DB_HOST = 'localhost';
$DB_NAME = 'form';
$DB_USER = 'root';
$DB_PASS = '';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
