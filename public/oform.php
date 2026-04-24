<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
require_once 'csrf.php';
$userId = $_SESSION['user_id'];

$from   = $_SESSION['delivery_from']   ?? '';
$to     = $_SESSION['delivery_to']     ?? '';
$weight = (int)($_SESSION['delivery_weight'] ?? 10);
$typeId = (int)($_SESSION['delivery_type']   ?? 1);

if (!$from || !$to) {
    header('Location: index.php?error=empty_address');
    exit;
}

$typeId_safe  = (int)$typeId;
$res          = mysqli_query($conn, "SELECT * FROM DeliveryTypes WHERE Id = $typeId_safe");
$deliveryType = mysqli_fetch_assoc($res);
$typeName     = $deliveryType['Name']  ?? 'Курьер';
$basePrice    = (int)($deliveryType['Price'] ?? 300);

$distanceKm = 5;
$price = $basePrice + ($weight * 5) + ($distanceKm * 10);

$orderPlaced = false;
$orderNum    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    csrf_verify();
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment'] ?? ''));

    $fromParts  = explode(',', $from);
    $fromStreet = mysqli_real_escape_string($conn, trim($fromParts[0] ?? $from));
    $fromHouse  = mysqli_real_escape_string($conn, trim($fromParts[1] ?? '1'));
    mysqli_query($conn, "INSERT INTO Addresses (UsersAndCouriersId, Street, House) VALUES ($userId, '$fromStreet', '$fromHouse')");
    $fromAddrId = mysqli_insert_id($conn);

    $toParts  = explode(',', $to);
    $toStreet = mysqli_real_escape_string($conn, trim($toParts[0] ?? $to));
    $toHouse  = mysqli_real_escape_string($conn, trim($toParts[1] ?? '1'));
    mysqli_query($conn, "INSERT INTO Addresses (UsersAndCouriersId, Street, House) VALUES ($userId, '$toStreet', '$toHouse')");
    $toAddrId = mysqli_insert_id($conn);

    $stRes    = mysqli_query($conn, "SELECT Id FROM OrderStatuses ORDER BY Id LIMIT 1");
    $stRow    = mysqli_fetch_assoc($stRes);
    $statusId = $stRow['Id'] ?? 1;

    $maxRes = mysqli_query($conn, "SELECT MAX(Id) + 1 AS next_num FROM Orders");
    $maxRow = mysqli_fetch_assoc($maxRes);
    $orderNum = (string)($maxRow['next_num'] ?? 1);
    $orderNum_safe = mysqli_real_escape_string($conn, $orderNum);

    mysqli_query($conn, "
        INSERT INTO Orders
            (OrderNumber, UsersAndCouriersId, ShipmentAddressId, DeliveryAddressId,
             DeliveryTypeId, StatusId, Weight, Price, Comment, ReceiptTime)
        VALUES
            ('$orderNum_safe', $userId, $fromAddrId, $toAddrId,
             $typeId_safe, $statusId, $weight, $price, '$comment', NOW())
    ");

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
    <title>Оформление доставки</title>
    <link rel="stylesheet" href="css/fonts.css"> 
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
                    <textarea class="field-textarea" name="comment"></textarea>
                </div>
                <div class="field-card">
                    <label class="field-label">Способ оплаты</label>
                    <div class="pay-options">
                        <label class="pay-option">
                            <input type="radio" name="payment" value="card" checked>
                            <span>Банковская карта</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="cash">
                            <span>Наличные курьеру</span>
                        </label>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="sbp">
                            <span>Перевод по номеру телефона</span>
                        </label>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn" onclick="history.back()">Назад</button>
                    <button type="submit" name="pay" class="btn btn-pay">Оплатить</button>
                </div>
                <?= csrf_field() ?>
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
                <p class="price-note">
                    Базовая цена <?= $basePrice ?> ₽ + вес (<?= $weight * 5 ?> ₽) + доставка (<?= $distanceKm * 10 ?> ₽)
                </p>
            </div>
        </div>

    </div>

</main>

<?php if ($orderPlaced): ?>
<div class="overlay">
    <div class="popup">
        <h2>Доставка оформлена!</h2>
        <p>Номер вашего заказа:<br><strong><?= htmlspecialchars($orderNum) ?></strong></p>
        <p>Заказ скоро будет передан в работу.</p>
        <a href="index.php" class="btn-popup">На главную</a>
    </div>
</div>
<?php endif; ?>
</body>
</html>