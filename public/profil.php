<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';   // убедись, что db.php подключает MySQL
$userId = $_SESSION['user_id'];
$saved  = false;
$error  = '';

// Загружаем данные из БД
$stmt = mysqli_prepare($conn, "SELECT * FROM UsersAndCouriers WHERE Id = ?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Сохранение
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');

    if (!$firstname || !$email) {
        $error = 'Имя и email обязательны.';
    } else {
        // Если загружено фото — сохраняем вместе с остальными данными
        if (!empty($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoData = file_get_contents($_FILES['photo']['tmp_name']);
            $stmt = mysqli_prepare($conn, 
                "UPDATE UsersAndCouriers SET FirstName=?, LastName=?, Email=?, Image=? WHERE Id=?");
            mysqli_stmt_bind_param($stmt, "ssssi",
                $firstname,
                $lastname,
                $email,
                $photoData,
                $userId
            );

            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE UsersAndCouriers SET FirstName=?, LastName=?, Email=? WHERE Id=?"
            );

            mysqli_stmt_bind_param($stmt, "sssi",
                $firstname,
                $lastname,
                $email,
                $userId
            );

            mysqli_stmt_execute($stmt);
        }

        $_SESSION['user_name']  = $firstname;
        $_SESSION['user_last']  = $lastname;
        $_SESSION['user_email'] = $email;

        // Перечитываем пользователя из БД
        $stmt = mysqli_prepare($conn, "SELECT * FROM UsersAndCouriers WHERE Id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        $saved = true;
    }
}

// Формируем src для фото из BLOB
$photoSrc = '';
if (!empty($user['Image'])) {
    $photoSrc = 'data:image/jpeg;base64,' . base64_encode($user['Image']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Профиль</title>
    <link rel="stylesheet" href="css/fonts.css"> 
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
                           value="<?= htmlspecialchars($user['FirstName'] ?? '') ?>">
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="text" name="lastname"
                           placeholder="Фамилия"
                           value="<?= htmlspecialchars($user['LastName'] ?? '') ?>">
                </div>
                <div class="field-box">
                    <input type="email" name="email"
                           placeholder="Электронная почта *"
                           value="<?= htmlspecialchars($user['Email'] ?? '') ?>">
                    <span class="star">*</span>
                </div>
                <div class="field-box">
                    <input type="tel" name="phone"
                           placeholder="Номер телефона"
                           value="<?= htmlspecialchars($user['Phone'] ?? '') ?>">
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
                    <span id="photoLabel" style="<?= $photoSrc ? 'display:none' : '' ?>">
                        Нажмите, чтобы<br>добавить фото
                    </span>
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