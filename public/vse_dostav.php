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

// Все доставки пользователя
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.ordernumber,
        o.weight,
        o.price,
        sa.street AS from_street, sa.house AS from_house,
        da.street AS to_street, da.house AS to_house,
        os.name AS status_name
    FROM orders o
    LEFT JOIN addresses sa ON o.shipmentaddressid = sa.id
    LEFT JOIN addresses da ON o.deliveryaddressid = da.id
    LEFT JOIN orderstatuses os ON o.statusid = os.id
    WHERE o.userid = ?
    ORDER BY o.id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все доставки — DeerGo</title>
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
                <a href="cour.php" class="tab-option">Для курьеров</a>
            </div>
            <a href="profil.php" class="profile-btn">Профиль</a>
        </nav>
    </header>

    <main class="main">
        <h1 class="page-title">История доставок</h1>
        <p class="page-subtitle">Все ваши заказы — прошлые и текущие</p>

        <div class="orders-layout">

            <!-- СПИСОК -->
            <div class="orders-list">
                <?php if (empty($orders)): ?>
                    <div class="empty-msg">У вас пока нет доставок</div>
                <?php else: ?>
                    <?php foreach ($orders as $i => $o): ?>
                    <div class="order-card <?= $i === 0 ? 'active' : '' ?>" onclick="selectOrder(<?= $i ?>)" data-index="<?= $i ?>">
                        <div class="order-num">№ <?= htmlspecialchars($o['ordernumber']) ?></div>
                        <div class="order-route">
                            <span class="route-label">Откуда:</span> <?= htmlspecialchars($o['from_street'] . ', ' . $o['from_house']) ?>
                        </div>
                        <div class="order-route">
                            <span class="route-label">Куда:</span> <?= htmlspecialchars($o['to_street'] . ', ' . $o['to_house']) ?>
                        </div>
                        <div class="order-price"><?= htmlspecialchars($o['price']) ?> ₽</div>
                        <div class="order-status <?= $o['status_name'] === 'Доставлено' ? 'status-done' : 'status-active' ?>">
                            <?= htmlspecialchars($o['status_name']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="btn-row">
                    <button class="btn" onclick="history.back()">Назад</button>
                </div>
            </div>

            <!-- ДЕТАЛИ -->
            <div class="order-detail">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $i => $o): ?>
                    <div class="detail-block <?= $i === 0 ? '' : 'hidden' ?>" id="detail-<?= $i ?>">
                        <div class="detail-title">Детали заказа</div>
                        <div class="detail-row"><span>Номер доставки:</span> <?= htmlspecialchars($o['ordernumber']) ?></div>
                        <div class="detail-row"><span>Откуда:</span> <?= htmlspecialchars($o['from_street'] . ', ' . $o['from_house']) ?></div>
                        <div class="detail-row"><span>Куда:</span> <?= htmlspecialchars($o['to_street'] . ', ' . $o['to_house']) ?></div>
                        <div class="detail-row"><span>Вес:</span> <?= htmlspecialchars($o['weight']) ?> кг</div>
                        <div class="detail-row"><span>Стоимость:</span> <?= htmlspecialchars($o['price']) ?> ₽</div>
                        <div class="detail-row"><span>Статус:</span>
                            <span class="badge <?= $o['status_name'] === 'Доставлено' ? 'status-done' : 'status-active' ?>">
                                <?= htmlspecialchars($o['status_name']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="detail-empty">Доставок пока нет</div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <script>
    function selectOrder(index) {
        document.querySelectorAll('.order-card').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.detail-block').forEach(d => d.classList.add('hidden'));
        document.querySelector('.order-card[data-index="' + index + '"]').classList.add('active');
        const detail = document.getElementById('detail-' + index);
        if (detail) detail.classList.remove('hidden');
    }
    </script>

</body>
</html>