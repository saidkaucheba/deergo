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
    <title>Карта — DeerGo</title>
    <link rel="stylesheet" href="css/track.css">
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
            <a href="profil.php" class="profile-btn">Профиль</a>
        </nav>
    </header>

    <main class="main">
        <h1 class="page-title">Отслеживание доставки</h1>
        <p class="page-subtitle">На карте отображается ориентировочное расположение курьера</p>

        <!-- КАРТА OPENSTREETMAP (бесплатная, без ключа) -->
        <div id="map"></div>

        <div class="btn-row">
            <button class="btn" onclick="history.back()">Назад</button>
        </div>
    </main>

    <!-- Leaflet — бесплатная карта -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Карта центрируется на Якутске
        const map = L.map('map').setView([62.0355, 129.6755], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Маркер — текущее место курьера (заглушка, в будущем заменить на реальные координаты)
        const courierMarker = L.marker([62.0355, 129.6755])
            .addTo(map)
            .bindPopup('Курьер здесь')
            .openPopup();
    </script>

</body>
</html>