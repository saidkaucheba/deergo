<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT o.Id, o.OrderNumber, o.Price,
           sa.Street AS from_street, sa.House AS from_house,
           da.Street AS to_street,   da.House AS to_house,
           os.Name AS status_name
    FROM Orders o
    JOIN Addresses sa ON sa.Id = o.ShipmentAddressId
    JOIN Addresses da ON da.Id = o.DeliveryAddressId
    JOIN OrderStatuses os ON os.Id = o.StatusId
    WHERE o.UserId = ?
    ORDER BY o.Id DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function statusColor($name) {
    if ($name === 'Доставлено') return '#4CAF50';
    if ($name === 'Отменено')   return '#f44336';
    return '#F5A623';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Все доставки</title>
    <link rel="stylesheet" href="css/vse_dostav.css">
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

    <h1 class="page-title">Все доставки</h1>
    <p class="page-sub">История всех ваших заказов в одном месте.</p>

    <div class="delivery-layout">

        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <div class="empty-msg">У вас ещё нет доставок</div>
            <?php else: ?>
                <?php foreach ($orders as $i => $ord): ?>
                    <div class="order-card <?= $i === 0 ? 'active' : '' ?>"
                         onclick="selectOrder(this, <?= $ord['Id'] ?>)">
                        <div class="order-num">Доставка №<?= htmlspecialchars($ord['ordernumber']) ?></div>
                        <div class="order-route">
                            <span class="route-label">Откуда:</span>
                            <?= htmlspecialchars($ord['from_street'] . ', ' . $ord['from_house']) ?>
                        </div>
                        <div class="order-route">
                            <span class="route-label">Куда:</span>
                            <?= htmlspecialchars($ord['to_street'] . ', ' . $ord['to_house']) ?>
                        </div>
                        <div class="order-status" style="color: <?= statusColor($ord['status_name']) ?>">
                            ● <?= htmlspecialchars($ord['status_name']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="right-col">
            <div class="detail-box">
                <div class="detail-inner" id="detailBox">
                    <?php if (!empty($orders)): $first = $orders[0]; ?>
                        <h2 class="detail-title">Доставка №<?= htmlspecialchars($first['ordernumber']) ?></h2>
                        <p><span class="route-label">Откуда:</span> <?= htmlspecialchars($first['from_street'] . ', ' . $first['from_house']) ?></p>
                        <p><span class="route-label">Куда:</span>   <?= htmlspecialchars($first['to_street']   . ', ' . $first['to_house'])   ?></p>
                        <p><span class="route-label">Стоимость:</span> <strong><?= htmlspecialchars($first['price']) ?> ₽</strong></p>
                        <p><span class="route-label">Статус:</span>
                            <strong style="color: <?= statusColor($first['status_name']) ?>">
                                <?= htmlspecialchars($first['status_name']) ?>
                            </strong>
                        </p>
                    <?php else: ?>
                        <p class="empty-msg">Нет доставок</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="btn-row">
                <button class="btn" onclick="history.back()">Назад</button>
                <a href="index.php" class="btn btn-new">Новый заказ</a>
            </div>
        </div>

    </div>

</main>

<script>
var ordersData = <?= json_encode($orders, JSON_UNESCAPED_UNICODE) ?>;

var statusColors = { 'Доставлено': '#4CAF50', 'Отменено': '#f44336' };

function getColor(name) {
    return statusColors[name] || '#F5A623';
}

function selectOrder(el, id) {
    document.querySelectorAll('.order-card').forEach(function(c) { c.classList.remove('active'); });
    el.classList.add('active');

    var ord = ordersData.find(function(o) { return o.Id == id || o.id == id; });
    if (!ord) return;

    document.getElementById('detailBox').innerHTML =
        '<h2 class="detail-title">Доставка №' + (ord.ordernumber || '') + '</h2>' +
        '<p><span class="route-label">Откуда:</span> ' + (ord.from_street || '') + ', ' + (ord.from_house || '') + '</p>' +
        '<p><span class="route-label">Куда:</span> '   + (ord.to_street   || '') + ', ' + (ord.to_house   || '') + '</p>' +
        '<p><span class="route-label">Стоимость:</span> <strong>' + (ord.price || 0) + ' ₽</strong></p>' +
        '<p><span class="route-label">Статус:</span> <strong style="color:' + getColor(ord.status_name) + '">' + (ord.status_name || '') + '</strong></p>';
}
</script>

</body>
</html>