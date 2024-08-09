<?php
require_once '../private/Database.php';
require_once '../private/UserValidator.php';
require_once '../private/utils.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$user = $result->fetch_assoc();

$success = false;
$error = '';

function handleProfile($conn, $user_id)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trimField($_POST['name']);
        $phone = trimField($_POST['phone']);
        $email = trimField($_POST['email']);
        $password = trimField($_POST['password']);
        $password_repeat = trimField($_POST['password_repeat']);

        UserValidator::validateName($name);
        UserValidator::validatePhone($phone);
        UserValidator::validateEmail($email);
        UserValidator::validatePassword($password);

        if ($password !== $password_repeat) {
            throw new RuntimeException('Passwords do not match');
        }

        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        checkFieldExistence($conn, $user_id, $name, 'name');
        checkFieldExistence($conn, $user_id, $phone, 'phone');
        checkFieldExistence($conn, $user_id, $email, 'email');

        $stmt = $conn->prepare('UPDATE users SET name = ?, phone = ?, email = ?, password = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $name, $phone, $email, $password_hashed, $user_id);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Profile update failed');
        }

        $stmt->close();
        return true;
    }
}

function checkFieldExistence($conn, $user_id, $field, $field_name)
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE $field_name = ? AND id != ?");
    $stmt->bind_param('si', $field, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $field_name = ucfirst($field_name);

    if ($result->num_rows === 1) {
        throw new RuntimeException("$field_name '$field' already exists");
    }
}


try {
    $success = handleProfile($conn, $user_id);
} catch (RuntimeException $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile</title>
</head>

<body>
    <h3>Profile info</h3>

    <?php if ($success): ?>
        <p>Profile updated successfully!</p>
    <?php endif; ?>

    <form action="profile.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_repeat">Repeat Password:</label>
        <input type="password" id="password_repeat" name="password_repeat" required><br><br>

        <?php if ($error): ?>
            <p style="color: red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <input type="submit" value="Update Profile">
    </form>
</body>

</html>
