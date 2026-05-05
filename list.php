<?php
require_once __DIR__ . '/config.php';

$error_message = '';
if (!empty($_GET['message'])) {
    $error_message = $_GET['message'];
}

$result = $mysqli->query('SELECT * FROM personal_details ORDER BY id DESC');
if (!$result) {
    error_log('List query failed: ' . $mysqli->error);
    $error_message = 'Unable to load records. Please try again later.';
    $result = null;
}

function format_interests($value) {
    return $value === '' ? '-' : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Saved Personal Details</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Saved Records</h1>
    <p><a href="form.php">Add new record</a></p>
    <?php if ($error_message): ?>
        <div class="error"><?php echo h($error_message); ?></div>
    <?php endif; ?>
    <?php if ($result && $result->num_rows === 0): ?>
        <p>No records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Gender</th>
                    <th>Interests</th>
                    <th>Birth Date</th>
                    <th>Rating</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo h($row['id']); ?></td>
                        <td><?php echo h($row['name']); ?></td>
                        <td><?php echo h($row['email']); ?></td>
                        <td><?php echo h($row['country']); ?></td>
                        <td><?php echo h($row['gender']); ?></td>
                        <td><?php echo format_interests($row['interests']); ?></td>
                        <td><?php echo h($row['birth_date']); ?></td>
                        <td><?php echo h($row['rating']); ?></td>
                        <td><?php echo h($row['created_at']); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo h($row['id']); ?>">Edit</a> |
                            <a href="delete.php?id=<?php echo h($row['id']); ?>" onclick="return confirm('Delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
