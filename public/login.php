<?php
// ===== ВСЯ ЛОГИКА ДО ВЫВОДА HTML =====
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$mode  = (isset($_GET['mode']) && $_GET['mode'] === 'register') ? 'register' : 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Заполните все поля.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['firstname'];
                $_SESSION['user_last']  = $user['lastname'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Неверный email или пароль.';
            }
        }
    }

    if ($action === 'register') {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password'] ?? '';

        if (!$firstname || !$email || !$password) {
            $error = 'Заполните обязательные поля (имя, email, пароль).';
            $mode  = 'register';
        } else {
            $stmt = $pdo->prepare("SELECT Id FROM Users WHERE Email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Этот email уже зарегистрирован.';
                $mode  = 'register';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO Users (Email, Password, FirstName, LastName) VALUES (?, ?, ?, ?) RETURNING Id"
                );
                $stmt->execute([$email, $hash, $firstname, $lastname]);
                $userId = $stmt->fetchColumn();

                $_SESSION['user_id']    = $userId;
                $_SESSION['user_name']  = $firstname;
                $_SESSION['user_last']  = $lastname;
                $_SESSION['user_email'] = $email;
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Вход</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<header class="header">
    <div class="header-left">
        <div class="logo-img">
            <img src="images/log.png" alt="логотип">
        </div>
        <span class="brand">DeerGo</span>
    </div>
</header>

<main class="main">
    <div class="auth-box">

        <div class="tab-switch">
            <a href="login.php?mode=login"    class="tab-option <?= $mode === 'login'    ? 'active' : '' ?>">Вход</a>
            <a href="login.php?mode=register" class="tab-option <?= $mode === 'register' ? 'active' : '' ?>">Регистрация</a>
        </div>

        <?php if ($error): ?>
            <p class="msg error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($mode === 'login'): ?>
        <form method="POST" class="auth-form">
            <input type="hidden" name="action" value="login">
            <div class="field-box">
                <input type="email" name="email" placeholder="Электронная почта" required>
            </div>
            <div class="field-box">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn-submit">Войти</button>
        </form>
        <p class="switch-link">Нет аккаунта? <a href="login.php?mode=register">Зарегистрироваться</a></p>

        <?php else: ?>
        <form method="POST" class="auth-form">
            <input type="hidden" name="action" value="register">
            <div class="field-box">
                <input type="text" name="firstname" placeholder="Имя *" required>
            </div>
            <div class="field-box">
                <input type="text" name="lastname" placeholder="Фамилия">
            </div>
            <div class="field-box">
                <input type="email" name="email" placeholder="Электронная почта *" required>
            </div>
            <div class="field-box">
                <input type="password" name="password" placeholder="Пароль *" required>
            </div>
            <button type="submit" class="btn-submit">Зарегистрироваться</button>
        </form>
        <p class="switch-link">Уже есть аккаунт? <a href="login.php?mode=login">Войти</a></p>
        <?php endif; ?>

    </div>
</main>

</body>
</html>