<?php
session_start();

// Подключение к базе данных
$host = 'localhost';
$dbname = 'deergo';
$user = 'postgres';
$password = '';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = 'Ошибка подключения к базе данных';
}

$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$error = '';
$success = '';

// ВХОД
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);

    if ($email && $password_input) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($password_input, $user_data['password'])) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_firstname'] = $user_data['firstname'];
            $_SESSION['user_lastname'] = $user_data['lastname'];
            $_SESSION['user_email'] = $user_data['email'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Неверный email или пароль';
        }
    } else {
        $error = 'Заполните все поля';
    }
}

// РЕГИСТРАЦИЯ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);

    if ($firstname && $email && $password_input) {
        // Проверяем, нет ли уже такого email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email уже существует';
        } else {
            $hash = password_hash($password_input, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $hash, $firstname, $lastname]);
            $new_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $new_id;
            $_SESSION['user_firstname'] = $firstname;
            $_SESSION['user_lastname'] = $lastname;
            $_SESSION['user_email'] = $email;
            header('Location: index.php');
            exit;
        }
    } else {
        $error = 'Заполните обязательные поля';
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

<div class="page-wrap">

    <!-- ШАПКА -->
    <header class="header">
        <div class="header-left">
            <div class="logo-img">
                <img src="images/log.png" alt="логотип">
            </div>
            <span class="brand">DeerGo</span>
        </div>
    </header>

    <!-- КАРТОЧКА ВХОДА/РЕГИСТРАЦИИ -->
    <main class="main">
        <div class="card-box">

            <!-- ПЕРЕКЛЮЧАТЕЛЬ -->
            <div class="tab-switch">
                <a href="login.php?page=login" class="tab-option <?= $page === 'login' ? 'active' : '' ?>">Вход</a>
                <a href="login.php?page=register" class="tab-option <?= $page === 'register' ? 'active' : '' ?>">Регистрация</a>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- ФОРМА ВХОДА -->
            <?php if ($page === 'login'): ?>
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
            <p class="switch-link">Нет аккаунта? <a href="login.php?page=register">Зарегистрироваться</a></p>

            <!-- ФОРМА РЕГИСТРАЦИИ -->
            <?php else: ?>
            <form method="POST" class="auth-form">
                <input type="hidden" name="action" value="register">
                <div class="field-box">
                    <input type="text" name="firstname" placeholder="Имя" required>
                </div>
                <div class="field-box">
                    <input type="text" name="lastname" placeholder="Фамилия">
                </div>
                <div class="field-box">
                    <input type="email" name="email" placeholder="Электронная почта" required>
                </div>
                <div class="field-box">
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <button type="submit" class="btn-submit">Зарегистрироваться</button>
            </form>
            <p class="switch-link">Уже есть аккаунт? <a href="login.php?page=login">Войти</a></p>
            <?php endif; ?>

        </div>

        <!-- ОПИСАНИЕ КОМПАНИИ -->
        <div class="promo-block">
            <h2>Быстрая и удобная доставка</h2>
            <p>DeerGo — сервис курьерской доставки. Отправляйте посылки по городу быстро, удобно и по выгодной цене.</p>
        </div>
    </main>

</div>

</body>
</html>