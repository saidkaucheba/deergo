<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
$userId = $_SESSION['user_id'];
$saved  = false;
$error  = '';

// Загружаем данные из БД
$stmt = $pdo->prepare("SELECT * FROM Users WHERE Id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Сохранение
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $phone     = trim($_POST['phone']     ?? '');

    if (!$firstname || !$email) {
        $error = 'Имя и email обязательны.';
    } else {
        $stmt = $pdo->prepare("UPDATE Users SET FirstName=?, LastName=?, Email=? WHERE Id=?");
        $stmt->execute([$firstname, $lastname, $email, $userId]);

        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoData = file_get_contents($_FILES['photo']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE Users SET Image=? WHERE Id=?");
            $stmt->execute([$photoData, $userId]);
        }

        $_SESSION['user_name']  = $firstname;
        $_SESSION['user_last']  = $lastname;
        $_SESSION['user_email'] = $email;

        $stmt = $pdo->prepare("SELECT * FROM Users WHERE Id = ?");
        $stmt->execute([$userId]);
        $user  = $stmt->fetch(PDO::FETCH_ASSOC);
        $saved = true;
    }
}

$photoSrc = '';
if (!empty($user['image'])) {
    $photoSrc = 'data:image/jpeg;base64,' . base64_encode($user['image']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Профиль</title>
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
            <a href="cour.php"  class="tab-option">Для курьеров</a>
        </div>
        <a href="profil.php" class="profile-btn active-btn">Профиль</a>
    </nav>
</header>

<main class="main">

    <h1 class="page-title">Мой профиль</h1>

    <?php if ($saved): ?>
        <p class="msg success">Данные успешно сохранены!</p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="msg error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="profile-row">

            <div class="fields-col">
                <div class="field-box">
                    <input type="text" name="firstname"
                           placeholder="Имя *"
                           value="<?= htmlspecialchars($user['firstname'] ?? '') ?>">
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="text" name="lastname"
                           placeholder="Фамилия"
                           value="<?= htmlspecialchars($user['lastname'] ?? '') ?>">
                </div>
                <div class="field-box">
                    <input type="email" name="email"
                           placeholder="Электронная почта *"
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="tel" name="phone"
                           placeholder="Номер телефона"
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="btn-row">
                    <button type="button" class="btn" onclick="history.back()">Назад</button>
                    <button type="submit" class="btn btn-save">Сохранить</button>
                </div>

                <a href="logout.php" class="logout-link">Выйти из аккаунта</a>
            </div>

            <div class="right-col">
                <div class="photo-box" onclick="document.getElementById('photoInput').click()">
                    <img src="<?= $photoSrc ?>" alt="" id="photoPreview"
                         style="<?= $photoSrc ? 'display:block' : 'display:none' ?>">
                    <span id="photoLabel" style="<?= $photoSrc ? 'display:none' : '' ?>">Нажмите, чтобы<br>добавить фото</span>
                    <input type="file" name="photo" accept="image/*" id="photoInput"
                           style="display:none" onchange="previewPhoto(this)">
                </div>
            </div>

        </div>
    </form>

</main>

<script>
function previewPhoto(input) {
    if (!input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('photoPreview').src = e.target.result;
        document.getElementById('photoPreview').style.display = 'block';
        document.getElementById('photoLabel').style.display   = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

</body>
</html>