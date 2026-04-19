<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$userId = (int)$_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT o.Id, o.OrderNumber,
           sa.Street AS from_street, sa.House AS from_house,
           da.Street AS to_street,   da.House AS to_house,
           os.Name   AS status_name
    FROM Orders o
    JOIN Addresses     sa ON sa.Id = o.ShipmentAddressId
    JOIN Addresses     da ON da.Id = o.DeliveryAddressId
    JOIN OrderStatuses os ON os.Id = o.StatusId
    WHERE o.UsersAndCouriersId = $userId
      AND os.Name NOT IN ('Доставлен', 'Отменено')
    ORDER BY o.Id DESC
");
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Текущие доставки</title>
    <link rel="stylesheet" href="css/tekuch.css">
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

    <h1 class="page-title">Текущие доставки</h1>
    <p class="page-sub">Здесь отображаются ваши заказы, которые сейчас в пути.</p>

    <div class="delivery-layout">

        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <div class="empty-msg">У вас нет активных доставок</div>
            <?php else: ?>
                <?php foreach ($orders as $i => $ord): ?>
                    <div class="order-card <?= $i === 0 ? 'active' : '' ?>"
                         onclick="selectOrder(this, <?= (int)$ord['Id'] ?>)">
                        <div class="order-num">Доставка №<?= htmlspecialchars($ord['OrderNumber']) ?></div>
                        <div class="order-route">
                            <span class="route-label">Откуда:</span>
                            <?= htmlspecialchars($ord['from_street'] . ', ' . $ord['from_house']) ?>
                        </div>
                        <div class="order-route">
                            <span class="route-label">Куда:</span>
                            <?= htmlspecialchars($ord['to_street'] . ', ' . $ord['to_house']) ?>
                        </div>
                        <div class="order-status">
                            Статус: <strong><?= htmlspecialchars($ord['status_name']) ?></strong>
                        </div>
                        <a href="karta.php?order=<?= (int)$ord['Id'] ?>" class="btn-map">К карте</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="right-col">
            <div class="detail-box">
                <div class="detail-inner" id="detailBox">
                    <?php if (!empty($orders)): $first = $orders[0]; ?>
                        <h2 class="detail-title">Доставка №<?= htmlspecialchars($first['OrderNumber']) ?></h2>
                        <p><span class="route-label">Откуда:</span>
                            <?= htmlspecialchars($first['from_street'] . ', ' . $first['from_house']) ?></p>
                        <p><span class="route-label">Куда:</span>
                            <?= htmlspecialchars($first['to_street']   . ', ' . $first['to_house']) ?></p>
                        <p><span class="route-label">Статус:</span>
                            <strong><?= htmlspecialchars($first['status_name']) ?></strong></p>
                        <a href="karta.php?order=<?= (int)$first['Id'] ?>" class="btn-map-big">Открыть карту</a>
                    <?php else: ?>
                        <p class="empty-msg">Нет активных доставок</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="btn-row">
                <button class="btn" onclick="history.back()">Назад</button>
            </div>
        </div>

    </div>

</main>

<script>
var ordersData = <?= json_encode($orders, JSON_UNESCAPED_UNICODE) ?>;

function selectOrder(el, id) {
    document.querySelectorAll('.order-card').forEach(function(c) { c.classList.remove('active'); });
    el.classList.add('active');

    var ord = ordersData.find(function(o) { return o.Id == id; });
    if (!ord) return;

    document.getElementById('detailBox').innerHTML =
        '<h2 class="detail-title">Доставка №' + (ord.OrderNumber || '') + '</h2>' +
        '<p><span class="route-label">Откуда:</span> ' + (ord.from_street || '') + ', ' + (ord.from_house || '') + '</p>' +
        '<p><span class="route-label">Куда:</span> '   + (ord.to_street   || '') + ', ' + (ord.to_house   || '') + '</p>' +
        '<p><span class="route-label">Статус:</span> <strong>' + (ord.status_name || '') + '</strong></p>' +
        '<a href="karta.php?order=' + id + '" class="btn-map-big">Открыть карту</a>';
}
</script>

</body>
</html>