<?php
require_once __DIR__ . '/config.php';

function redirect_with_message(string $message) {
    header('Location: list.php?error=server_error&message=' . urlencode($message));
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php?error=invalid_id&message=' . urlencode('Invalid record id.'));
    exit;
}
$id = (int) $_GET['id'];

$stmt = $mysqli->prepare('DELETE FROM personal_details WHERE id = ?');
if (!$stmt) {
    error_log('Delete prepare failed: ' . $mysqli->error);
    redirect_with_message('Unable to delete record. Please try again later.');
}
$stmt->bind_param('i', $id);
if (!$stmt->execute()) {
    error_log('Delete execute failed: ' . $stmt->error);
    $stmt->close();
    redirect_with_message('Unable to delete record. Please try again later.');
}
if ($stmt->affected_rows === 0) {
    $stmt->close();
    header('Location: list.php?error=not_found&message=' . urlencode('Record not found.'));
    exit;
}
$stmt->close();

header('Location: list.php');
exit;
