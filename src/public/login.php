<?php
require_once '../private/Database.php';
require_once '../private/utils.php';

session_start();

const CAPTCHA_URL = 'https://smartcaptcha.yandexcloud.net/';
$error = '';

function handleLogin()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trimField($_POST['login']);
        $password = trimField($_POST['password']);
        $captchaToken = $_POST['smart-token'];

        checkCaptcha($captchaToken);

        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('SELECT * FROM users WHERE phone = ? OR email = ?');
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows !== 1) {
            throw new RuntimeException('Invalid credentials');
        }

        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            throw new RuntimeException('Invalid credentials');
        }

        $_SESSION['user_id'] = $user['id'];
        header('Location: profile.php');
    }
}

function checkCaptcha($token)
{
    $secret = getenv('CAPTCHA_SECRET_KEY');

    $captcha_validation = file_get_contents(CAPTCHA_URL . "validate?secret=$secret&token=$token");
    $captcha_validation = json_decode($captcha_validation);

    if (!$captcha_validation || $captcha_validation->status !== 'ok') {
        throw new RuntimeException('Captcha validation failed');
    }
}

try {
    handleLogin();
} catch (RuntimeException $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <script src="<?= CAPTCHA_URL . 'captcha.js' ?>" defer></script>
</head>

<body>
    <?php require_once '../private/templates/header.php'; ?>

    <h3>Login</h3>

    <form action="login.php" method="post">
        <label for="login">Phone or Email:</label>
        <input type="text" id="login" name="login" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <div id="captcha-container" class="smart-captcha" style="width: 300px; height: 100px"
            data-sitekey="<?= getenv('CAPTCHA_CLIENT_KEY') ?>"></div><br>

        <?php if ($error): ?>
            <p style="color: red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <input type="submit" value="Login">
    </form>
</body>

</html>
