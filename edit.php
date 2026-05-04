<?php
require_once __DIR__ . '/config.php';

function render_error_page(string $message, int $code = 400) {
    http_response_code($code);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Error</title><style>body{font-family:Arial,sans-serif;max-width:760px;margin:24px auto;} .error{color:#b00;margin-bottom:16px;}</style></head><body><h1>Error</h1><div class="error">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div><p><a href="list.php">Back to list</a></p></body></html>';
    exit;
}

$errors = [];
$message = '';
$old = [
    'name' => '',
    'email' => '',
    'message' => '',
    'country' => '',
    'gender' => '',
    'interests' => [],
    'birth_date' => '',
    'rating' => '5',
];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    render_error_page('Invalid record id.');
}
$id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $interests = isset($_POST['interests']) && is_array($_POST['interests']) ? array_map('trim', $_POST['interests']) : [];
    $birth_date = trim($_POST['birth_date'] ?? '');
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;

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

    if (empty($errors)) {
        $stmt = $mysqli->prepare('UPDATE personal_details SET name = ?, email = ?, message = ?, country = ?, gender = ?, interests = ?, birth_date = ?, rating = ? WHERE id = ?');
        if (!$stmt) {
            error_log('Edit prepare failed: ' . $mysqli->error);
            $message = 'Unable to update the record. Please try again later.';
        } else {
            $interests_str = implode(',', $interests);
            $stmt->bind_param('sssssssii', $name, $email, $message, $country, $gender, $interests_str, $birth_date, $rating, $id);
            if (!$stmt->execute()) {
                error_log('Edit execute failed: ' . $stmt->error);
                $message = 'Unable to update the record. Please try again later.';
            }
            $stmt->close();
        }

        if ($message === '') {
            header('Location: list.php');
            exit;
        }
    }
} else {
    $stmt = $mysqli->prepare('SELECT * FROM personal_details WHERE id = ?');
    if (!$stmt) {
        error_log('Select prepare failed: ' . $mysqli->error);
        render_error_page('Unable to load the selected record. Please try again later.', 500);
    }
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        error_log('Select execute failed: ' . $stmt->error);
        $stmt->close();
        render_error_page('Unable to load the selected record. Please try again later.', 500);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        render_error_page('Record not found.', 404);
    }

    $old = [
        'name' => $row['name'],
        'email' => $row['email'],
        'message' => $row['message'],
        'country' => $row['country'],
        'gender' => $row['gender'],
        'interests' => $row['interests'] ? explode(',', $row['interests']) : [],
        'birth_date' => $row['birth_date'],
        'rating' => $row['rating'],
    ];
}

$countries = ['' => '-- Select Country --', 'India' => 'India', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada'];
$genders = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'];
$interestOptions = ['sports' => 'Sports', 'music' => 'Music', 'reading' => 'Reading', 'travel' => 'Travel'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Record</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 760px; margin: 24px auto; }
        label { display: block; margin: 12px 0 4px; }
        input[type="text"], input[type="email"], input[type="date"], textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .field-group { margin-bottom: 16px; }
        .checkbox-group input { margin-right: 8px; }
        .error { color: #b00; margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>Edit Record</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <strong>Please fix these fields:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo h($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($message)): ?>
        <div class="error"><?php echo h($message); ?></div>
    <?php endif; ?>
    <form action="edit.php?id=<?php echo h($id); ?>" method="post">
        <div class="field-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo h($old['name']); ?>" required>
        </div>
        <div class="field-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo h($old['email']); ?>" required>
        </div>
        <div class="field-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4"><?php echo h($old['message']); ?></textarea>
        </div>
        <div class="field-group">
            <label for="country">Country</label>
            <select id="country" name="country" required>
                <?php foreach ($countries as $value => $label): ?>
                    <option value="<?php echo h($value); ?>" <?php echo $old['country'] === $value ? 'selected' : ''; ?>><?php echo h($label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Gender</label>
            <?php foreach ($genders as $value => $label): ?>
                <label><input type="radio" name="gender" value="<?php echo h($value); ?>" <?php echo $old['gender'] === $value ? 'checked' : ''; ?>> <?php echo h($label); ?></label>
            <?php endforeach; ?>
        </div>
        <div class="field-group checkbox-group">
            <label>Interests</label>
            <?php foreach ($interestOptions as $value => $label): ?>
                <label><input type="checkbox" name="interests[]" value="<?php echo h($value); ?>" <?php echo in_array($value, $old['interests'], true) ? 'checked' : ''; ?>> <?php echo h($label); ?></label>
            <?php endforeach; ?>
        </div>
        <div class="field-group">
            <label for="birth_date">Birth Date</label>
            <input type="date" id="birth_date" name="birth_date" value="<?php echo h($old['birth_date']); ?>" required>
        </div>
        <div class="field-group">
            <label for="rating">Rating: <span id="ratingValue"><?php echo h($old['rating']); ?></span></label>
            <input type="range" id="rating" name="rating" min="1" max="10" value="<?php echo h($old['rating']); ?>" oninput="document.getElementById('ratingValue').textContent = this.value;">
        </div>
        <button type="submit">Update</button>
    </form>
    <p><a href="list.php">Back to list</a></p>
</body>
</html>
