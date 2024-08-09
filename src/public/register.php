<?php
require_once '../private/Database.php';
require_once '../private/UserValidator.php';
require_once '../private/utils.php';

$success = false;
$error = '';
$name = '';
$phone = '';
$email = '';

function handleRegistration()
{
    global $name, $phone, $email;

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

        $db = Database::getInstance();
        $conn = $db->getConnection();

        checkFieldExistence($conn, $name, 'name');
        checkFieldExistence($conn, $phone, 'phone');
        checkFieldExistence($conn, $email, 'email');

        $stmt = $conn->prepare('INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $phone, $email, $password_hashed);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Registration failed');
        }

        $stmt->close();
        return true;
    }
}

function checkFieldExistence($conn, $field, $field_name)
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE $field_name = ?");
    $stmt->bind_param('s', $field);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $field_name = ucfirst($field_name);

    if ($result->num_rows === 1) {
        throw new RuntimeException("$field_name '$field' already exists");
    }
}

try {
    $success = handleRegistration();
} catch (RuntimeException $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body>
    <?php require_once '../private/templates/header.php'; ?>

    <main>
        <h3>Registration</h3>

        <?php if ($success): ?>
            <p>Registration successful!</p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="password_repeat">Repeat Password:</label>
            <input type="password" id="password_repeat" name="password_repeat" required><br><br>

            <?php if ($error): ?>
                <p style="color: red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <input type="submit" value="Register">
        </form>
    </main>
</body>

</html>
