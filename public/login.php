<?php
session_start();
require_once 'csrf.php';
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$mode  = (isset($_GET['mode']) && $_GET['mode'] === 'register') ? 'register' : 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Заполните все поля.';
        } else {
            $email_safe = mysqli_real_escape_string($conn, $email);
            $result = mysqli_query($conn, "SELECT * FROM UsersAndCouriers WHERE Email = '$email_safe'");
            $user   = mysqli_fetch_assoc($result);

            if ($user && password_verify($password, $user['Password'])) {
                $_SESSION['user_id']    = $user['Id'];
                $_SESSION['user_name']  = $user['FirstName'];
                $_SESSION['user_last']  = $user['LastName'];
                $_SESSION['user_email'] = $user['Email'];
                $_SESSION['user_role']  = $user['Role'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Неверный email или пароль.';
            }
        }
    }

    if ($action === 'register') {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname  = trim($_POST['lastname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $phone     = trim($_POST['phone'] ?? '');

        if (!$firstname || !$email || !$password) {
            $error = 'Заполните обязательные поля (имя, email, пароль).';
            $mode  = 'register';
        } else {
            $email_safe = mysqli_real_escape_string($conn, $email);
            $check = mysqli_query($conn, "SELECT Id FROM UsersAndCouriers WHERE Email = '$email_safe'");

            if (mysqli_fetch_assoc($check)) {
                $error = 'Этот email уже зарегистрирован.';
                $mode  = 'register';
            } else {
                $hash      = password_hash($password, PASSWORD_DEFAULT);
                $fn_safe   = mysqli_real_escape_string($conn, $firstname);
                $ln_safe   = mysqli_real_escape_string($conn, $lastname);
                $ph_safe   = mysqli_real_escape_string($conn, $phone);
                $hash_safe = mysqli_real_escape_string($conn, $hash);

                mysqli_query($conn, "
                    INSERT INTO UsersAndCouriers (Email, Password, FirstName, LastName, Phone)
                    VALUES ('$email_safe', '$hash_safe', '$fn_safe', '$ln_safe', '$ph_safe')
                ");

                $_SESSION['user_id']    = mysqli_insert_id($conn);
                $_SESSION['user_name']  = $firstname;
                $_SESSION['user_last']  = $lastname;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role']  = null;

                header('Location: index.php');
                exit;
            }
        }
    }
}
?>


</header>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="css/fonts.css"> 
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
            <a href="login.php?mode=login" class="tab-option <?= $mode === 'login' ? 'active' : '' ?>">Вход</a>
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
                <?= csrf_field() ?>
            </form>

            <p class="switch-link">
                Нет аккаунта? <a href="login.php?mode=register">Зарегистрироваться</a>
            </p>

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
                    <input type="tel" name="phone" placeholder="Номер телефона">
                </div>

                <div class="field-box">
                    <input type="password" name="password" placeholder="Пароль *" required>
                </div>

                <button type="submit" class="btn-submit">Зарегистрироваться</button>
                <?= csrf_field() ?>
            </form>

            <p class="switch-link">
                Уже есть аккаунт? <a href="login.php?mode=login">Войти</a>
            </p>
        <?php endif; ?>

    </div>
</main>

</body>
</html>