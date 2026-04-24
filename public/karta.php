<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$userId  = (int)$_SESSION['user_id'];
$orderId = (int)($_GET['order'] ?? 0);

$orderInfo = null;
if ($orderId > 0) {
    $result = mysqli_query($conn, "
        SELECT o.OrderNumber,
               sa.Street AS from_street, sa.House AS from_house,
               da.Street AS to_street,   da.House AS to_house,
               os.Name   AS status_name
        FROM Orders o
        JOIN Addresses     sa ON sa.Id = o.ShipmentAddressId
        JOIN Addresses     da ON da.Id = o.DeliveryAddressId
        JOIN OrderStatuses os ON os.Id = o.StatusId
        WHERE o.Id = $orderId AND o.UsersAndCouriersId = $userId
        LIMIT 1
    ");
    $orderInfo = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Карта</title>
    <link rel="stylesheet" href="css/fonts.css"> 
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

    <?php if ($orderInfo): ?>
        <p class="page-sub">
            Заказ №<?= htmlspecialchars($orderInfo['OrderNumber']) ?> —
            <?= htmlspecialchars($orderInfo['from_street'] . ', ' . $orderInfo['from_house']) ?>
            → <?= htmlspecialchars($orderInfo['to_street'] . ', ' . $orderInfo['to_house']) ?>
        </p>
    <?php else: ?>
        <p class="page-sub">Здесь будет отображаться текущее местоположение вашей доставки.</p>
    <?php endif; ?>

    <div class="map-layout">
        <div class="map-box" id="map"></div>

        <div class="map-info">
            <div class="info-card">
                <div class="info-icon">📦</div>
                <h2>Отслеживание</h2>
                <?php if ($orderInfo): ?>
                    <p><strong>Статус:</strong> <?= htmlspecialchars($orderInfo['status_name']) ?></p>
                    <p><strong>Откуда:</strong> <?= htmlspecialchars($orderInfo['from_street'] . ', ' . $orderInfo['from_house']) ?></p>
                    <p><strong>Куда:</strong> <?= htmlspecialchars($orderInfo['to_street'] . ', ' . $orderInfo['to_house']) ?></p>
                <?php else: ?>
                    <p>Функция отслеживания в реальном времени будет доступна после назначения курьера.</p>
                <?php endif; ?>
            </div>
            <div class="btn-row">
                <button class="btn" onclick="history.back()">Назад</button>
            </div>
        </div>
    </div>

</main>

<script src="https://api-maps.yandex.ru/2.1/?apikey=b9162b22-8464-4258-90f7-1dbb2bcb3717&lang=ru_RU" type="text/javascript"></script>
<script>
ymaps.ready(function () {
    var map = new ymaps.Map('map', {
        center: [62.0355, 129.6755],
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