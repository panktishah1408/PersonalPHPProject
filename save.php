<?php
require_once __DIR__ . '/config.php';

function sanitize_text($value) {
    return trim($value);
}

function redirect_with_errors(array $errors, array $old) {
    header('Location: form.php?error=' . urlencode(implode(',', $errors)) . '&old=' . urlencode(json_encode($old)));
    exit;
}

function redirect_with_message(string $message, array $old) {
    header('Location: form.php?error=server_error&message=' . urlencode($message) . '&old=' . urlencode(json_encode($old)));
    exit;
}

$name = isset($_POST['name']) ? sanitize_text($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_text($_POST['email']) : '';
$message = isset($_POST['message']) ? sanitize_text($_POST['message']) : '';
$country = isset($_POST['country']) ? sanitize_text($_POST['country']) : '';
$gender = isset($_POST['gender']) ? sanitize_text($_POST['gender']) : '';
$interests = isset($_POST['interests']) && is_array($_POST['interests']) ? array_map('sanitize_text', $_POST['interests']) : [];
$birth_date = isset($_POST['birth_date']) ? sanitize_text($_POST['birth_date']) : '';
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;

$errors = [];
if ($name === '') {
    $errors[] = 'name';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
if ($country === '') {
    $errors[] = 'country';
}
if ($gender === '') {
    $errors[] = 'gender';
}
if ($birth_date === '') {
    $errors[] = 'birth_date';
}

$old = [
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'country' => $country,
    'gender' => $gender,
    'interests' => $interests,
    'birth_date' => $birth_date,
    'rating' => $rating,
];

if (!empty($errors)) {
    redirect_with_errors($errors, $old);
}

$interests_str = implode(',', $interests);
$stmt = $mysqli->prepare('INSERT INTO personal_details (name, email, message, country, gender, interests, birth_date, rating, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
if (!$stmt) {
    error_log('Save prepare failed: ' . $mysqli->error);
    redirect_with_message('Unable to save your record. Please try again later.', $old);
}
$stmt->bind_param('sssssssi', $name, $email, $message, $country, $gender, $interests_str, $birth_date, $rating);
if (!$stmt->execute()) {
    error_log('Save execute failed: ' . $stmt->error);
    $stmt->close();
    redirect_with_message('Unable to save your record. Please try again later.', $old);
}
$stmt->close();

header('Location: form.php?saved=1');
exit;
