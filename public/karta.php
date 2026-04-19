<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Карта</title>
    <link rel="stylesheet" href="css/karta.css">
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
        <a href="profil.php" class="profile-btn">Профиль</a>
    </nav>
</header>

<main class="main">

    <h1 class="page-title">Карта доставки</h1>
    <p class="page-sub">Здесь будет отображаться текущее местоположение вашей доставки.</p>

    <div class="map-layout">
        <div class="map-box" id="map"></div>

        <div class="map-info">
            <div class="info-card">
                <div class="info-icon">📦</div>
                <h2>Отслеживание</h2>
                <p>Функция отслеживания в реальном времени будет доступна после назначения курьера.</p>
            </div>
            <div class="info-card">
                <div class="info-icon">📍</div>
                <h2>Ваш город</h2>
                <p>Якутск, Республика Саха (Якутия)</p>
            </div>
            <div class="btn-row">
                <button class="btn" onclick="history.back()">Назад</button>
            </div>
        </div>
    </div>

</main>

<!--
    Для работы карты получите бесплатный ключ на:
    https://developer.tech.yandex.ru/
    и замените ВАШ_КЛЮЧ_ЯНДЕКС ниже.
-->
<script src="https://api-maps.yandex.ru/2.1/?apikey=b9162b22-8464-4258-90f7-1dbb2bcb3717&lang=ru_RU" type="text/javascript"></script>
<script>
ymaps.ready(function () {
    var map = new ymaps.Map('map', {
        center: [62.0355, 129.6755], // Якутск
        zoom: 12,
        controls: ['zoomControl', 'fullscreenControl']
    });

    var placemark = new ymaps.Placemark([62.0355, 129.6755], {
        balloonContent: 'DeerGo — служба доставки'
    }, {
        preset: 'islands#darkBlueDeliveryIcon'
    });

    map.geoObjects.add(placemark);
});
</script>

</body>
</html>