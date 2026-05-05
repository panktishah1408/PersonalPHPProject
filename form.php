<?php
require_once __DIR__ . '/config.php';

$errors = [];
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

if (!empty($_GET['error'])) {
    $errors = explode(',', $_GET['error']);
    if (!empty($_GET['old'])) {
        $old = json_decode(urldecode($_GET['old']), true);
    }
}

$error_message = '';
if (!empty($_GET['message'])) {
    $error_message = $_GET['message'];
}

$countries = ['' => '-- Select Country --', 'India' => 'India', 'USA' => 'USA', 'UK' => 'UK', 'Canada' => 'Canada'];
$genders = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'];
$interestOptions = ['sports' => 'Sports', 'music' => 'Music', 'reading' => 'Reading', 'travel' => 'Travel'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Details Form</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="form-page">
    <h1>Personal Details Form</h1>
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
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo h($error_message); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['saved'])): ?>
        <div class="success">Record saved successfully.</div>
    <?php endif; ?>
    <form action="save.php" method="post">
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
        <button type="submit">Submit</button>
    </form>
    <p><a href="list.php">View saved records</a></p>
</body>
</html>
