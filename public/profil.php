<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost'; $dbname = 'deergo'; $user = 'postgres'; $password = '';
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка БД');
}

$success = '';
$error = '';

// Загружаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Сохранение данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');

    $image_data = $user_data['image'];

    // Если загружена новая фотография
    if (!empty($_FILES['photo']['tmp_name'])) {
        $image_data = file_get_contents($_FILES['photo']['tmp_name']);
        $image_data = base64_encode($image_data);
    }

    if ($firstname && $email) {
        $stmt = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, email=?, image=? WHERE id=?");
        $stmt->execute([$firstname, $lastname, $email, $image_data, $_SESSION['user_id']]);
        $_SESSION['user_firstname'] = $firstname;
        $_SESSION['user_lastname'] = $lastname;
        $_SESSION['user_email'] = $email;
        $success = 'Данные сохранены!';
        // Обновляем локальные данные
        $user_data['firstname'] = $firstname;
        $user_data['lastname'] = $lastname;
        $user_data['email'] = $email;
        $user_data['image'] = $image_data;
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
    <title>Профиль — DeerGo</title>
    <link rel="stylesheet" href="css/profil.css">
</head>
<body>

    <header class="header">
        <div class="header-left">
            <div class="logo-img">
                <img src="images/log.png" alt="логотип">
            </div>
            <span class="brand">DeerGo</span>
        </div>
        <nav class="nav-menu">
            <div class="tab-switch">
                <a href="index.php" class="tab-option">Главная</a>
                <a href="cour.php" class="tab-option">Для курьеров</a>
            </div>
            <a href="profil.php" class="profile-btn active-btn">Профиль</a>
        </nav>
    </header>

    <main class="main">
        <h1 class="page-title">Мой профиль</h1>

        <?php if ($success): ?>
            <div class="msg-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
        <div class="profile-row">

            <!-- ПОЛЯ -->
            <div class="fields-col">
                <div class="field-box">
                    <input type="text" name="firstname" placeholder="Имя" value="<?= htmlspecialchars($user_data['firstname'] ?? '') ?>" required>
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="text" name="lastname" placeholder="Фамилия" value="<?= htmlspecialchars($user_data['lastname'] ?? '') ?>">
                </div>
                <div class="field-box">
                    <input type="email" name="email" placeholder="Электронная почта" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="tel" name="phone" placeholder="Номер телефона" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                </div>

                <div class="btn-row">
                    <button type="button" class="btn" onclick="history.back()">Назад</button>
                    <button type="submit" class="btn btn-save">Сохранить</button>
                    <a href="logout.php" class="btn btn-logout">Выйти</a>
                </div>
            </div>

            <!-- ФОТО -->
            <div class="right-col">
                <div class="photo-box" id="photoBox">
                    <?php if (!empty($user_data['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= $user_data['image'] ?>" alt="Фото" id="photoPreview" style="display:block;">
                        <span id="photoLabel" style="display:none;">Фотография</span>
                    <?php else: ?>
                        <img src="" alt="" id="photoPreview">
                        <span id="photoLabel">Фотография</span>
                    <?php endif; ?>
                    <input type="file" name="photo" accept="image/*" id="photoInput">
                </div>
            </div>

        </div>
        </form>
    </main>

    <script>
    // Предпросмотр фото до сохранения
    document.getElementById('photoInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('photoLabel').style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
    </script>

</body>
</html>