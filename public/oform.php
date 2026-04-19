<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$userId = $_SESSION['user_id'];

$from   = $_SESSION['delivery_from']   ?? '';
$to     = $_SESSION['delivery_to']     ?? '';
$weight = (int)($_SESSION['delivery_weight'] ?? 10);
$typeId = (int)($_SESSION['delivery_type']   ?? 1);

if (!$from || !$to) {
    header('Location: index.php?error=empty_address');
    exit;
}

// Данные о типе доставки
$stmt = $pdo->prepare("SELECT * FROM DeliveryTypes WHERE Id = ?");
$stmt->execute([$typeId]);
$deliveryType = $stmt->fetch(PDO::FETCH_ASSOC);
$typeName  = $deliveryType['name']  ?? 'Курьер';
$basePrice = (int)($deliveryType['price'] ?? 300);

// Расчёт стоимости: базовая + вес*5 + расстояние*10 (расстояние — заглушка 5 км)
$distanceKm = 5;
$price = $basePrice + ($weight * 5) + ($distanceKm * 10);

$orderPlaced = false;
$orderNum    = '';

// Оплата
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $comment = trim($_POST['comment'] ?? '');

    // Адрес «Откуда»
    $fromParts  = explode(',', $from);
    $fromStreet = trim($fromParts[0] ?? $from);
    $fromHouse  = trim($fromParts[1] ?? '1');
    $stmt = $pdo->prepare("INSERT INTO Addresses (UserId, Street, House) VALUES (?, ?, ?) RETURNING Id");
    $stmt->execute([$userId, $fromStreet, $fromHouse]);
    $fromAddrId = $stmt->fetchColumn();

    // Адрес «Куда»
    $toParts  = explode(',', $to);
    $toStreet = trim($toParts[0] ?? $to);
    $toHouse  = trim($toParts[1] ?? '1');
    $stmt = $pdo->prepare("INSERT INTO Addresses (UserId, Street, House) VALUES (?, ?, ?) RETURNING Id");
    $stmt->execute([$userId, $toStreet, $toHouse]);
    $toAddrId = $stmt->fetchColumn();

    // Статус «Новый» — первый по Id
    $stmtS    = $pdo->query("SELECT Id FROM OrderStatuses ORDER BY Id LIMIT 1");
    $statusId = $stmtS->fetchColumn() ?: 1;

    // Номер заказа
    $orderNum = 'DG-' . date('Ymd') . '-' . rand(1000, 9999);

    // Запись заказа
    $stmt = $pdo->prepare("
        INSERT INTO Orders
            (OrderNumber, UserId, ShipmentAddressId, DeliveryAddressId,
             DeliveryTypeId, StatusId, Weight, Price, Comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$orderNum, $userId, $fromAddrId, $toAddrId, $typeId, $statusId, $weight, $price, $comment]);

    // Очищаем сессию
    unset($_SESSION['delivery_from'], $_SESSION['delivery_to'],
          $_SESSION['delivery_weight'], $_SESSION['delivery_type']);

    $orderPlaced = true;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Оформление доставки</title>
    <link rel="stylesheet" href="css/oform.css">
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

    <h1 class="page-title">Оформление доставки</h1>
    <p class="page-sub">Проверьте детали и оплатите заказ.</p>

    <div class="oform-layout">

        <div class="left-col">
            <form method="POST">
                <div class="field-card">
                    <label class="field-label">Комментарий к доставке</label>
                    <textarea class="field-textarea" name="comment"
                              placeholder="Например: позвонить за 10 минут, оставить у двери..."></textarea>
                </div>

                <div class="field-card">
                    <label class="field-label">Способ оплаты</label>
                    <div class="pay-options">
                        <label class="pay-option">
                            <input type="radio" name="payment" value="card" checked>
                            <span>💳 Банковская карта</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="cash">
                            <span>💵 Наличные курьеру</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="sbp">
                            <span>📲 СБП</span>
                        </label>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn" onclick="history.back()">Назад</button>
                    <button type="submit" name="pay" class="btn btn-pay">Оплатить</button>
                </div>
            </form>
        </div>

        <div class="right-col">
            <div class="summary-card">
                <h2 class="summary-title">Детали заказа</h2>
                <div class="summary-row">
                    <span>Откуда:</span>
                    <strong><?= htmlspecialchars($from) ?></strong>
                </div>
                <div class="summary-row">
                    <span>Куда:</span>
                    <strong><?= htmlspecialchars($to) ?></strong>
                </div>
                <div class="summary-row">
                    <span>Вид доставки:</span>
                    <strong><?= htmlspecialchars($typeName) ?></strong>
                </div>
                <div class="summary-row">
                    <span>Вес:</span>
                    <strong>до <?= $weight ?> кг</strong>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-price">
                    <span>Итого:</span>
                    <strong><?= $price ?> ₽</strong>
                </div>
                <p class="price-note">Базовая цена <?= $basePrice ?> ₽ + вес (<?= $weight * 5 ?> ₽) + доставка (<?= $distanceKm * 10 ?> ₽)</p>
            </div>
        </div>

    </div>

</main>

<?php if ($orderPlaced): ?>
<div class="overlay">
    <div class="popup">
        <div class="popup-icon">✅</div>
        <h2>Доставка оформлена!</h2>
        <p>Номер вашего заказа:<br><strong><?= htmlspecialchars($orderNum) ?></strong></p>
        <p>Заказ скоро будет передан в работу.</p>
        <a href="index.php" class="btn-popup">На главную</a>
    </div>
</div>
<?php endif; ?>

</body>
</html>