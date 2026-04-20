<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['delivery_from']   = $_POST['from']    ?? '';
    $_SESSION['delivery_to']     = $_POST['to']      ?? '';
    $_SESSION['delivery_weight'] = $_POST['weight']  ?? 10;
    $_SESSION['delivery_type']   = $_POST['type_id'] ?? 1;
    header('Location: oform.php');
    exit;
}

$result        = mysqli_query($conn, "SELECT * FROM DeliveryTypes ORDER BY Id");
$deliveryTypes = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Главная</title>
    <link rel="stylesheet" href="css/glav.css">
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
            <a href="index.php" class="tab-option active">Главная</a>
            <a href="cour.php" class="tab-option">Для курьеров</a>
        </div>
        <a href="profil.php" class="profile-btn">Профиль</a>
    </nav>
</header>

<main class="main">

    <h1 class="page-title">Быстрая доставка по всему городу</h1>
    <p class="page-sub">Выберите адреса и параметры — мы доставим быстро и бережно.</p>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'empty_address'): ?>
        <p class="error-msg">⚠️ Пожалуйста, введите адреса «Откуда» и «Куда».</p>
    <?php endif; ?>

    <div class="top-row">

        <div class="left-panel">
            <form method="POST" id="orderForm">

                <div class="inputs-row">
                    <div class="input-box">
                        <input type="text" name="from" id="fromInput" placeholder="Откуда" required>
                        <button type="button" class="pin-btn">
                            <img src="images/otkuda.png" alt="">
                        </button>
                    </div>

                    <div class="input-box">
                        <input type="text" name="to" id="toInput" placeholder="Куда" required>
                        <button type="button" class="pin-btn">
                            <img src="images/otkuda.png" alt="">
                        </button>
                    </div>
                </div>

                <input type="hidden" name="weight" id="weightVal" value="10">
                <input type="hidden" name="type_id" id="typeIdVal" value="<?= $deliveryTypes[0]['Id'] ?? 1 ?>">

                <!-- ВЕС -->
                <div class="section">
                    <div class="page-title ">Размер доставки</div>
                    <div class="cards-row" id="size-group">
                        <?php foreach ([10,20,50,100] as $i => $w): ?>
                            <div class="card <?= $i === 0 ? 'active' : '' ?>" data-weight="<?= $w ?>">
                                <span>до <?= $w ?> кг</span>
                                <div class="check">✔</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ТИП -->
                <div class="section">
                    <div class="page-title ">Вид доставки</div>
                    <div class="cards-row" id="type-group">
                        <?php
                        $typeImages = [
                            'Курьер' => 'courier.png',
                            'Легковая машина' => 'car.png',
                            'Фургон' => 'gruzov.png',
                            'Грузовая' => 'bolsh.png',
                        ];
                        foreach ($deliveryTypes as $i => $dt):
                            $img = $typeImages[$dt['Name']] ?? 'courier.png';
                        ?>
                        <div class="card <?= $i === 0 ? 'active' : '' ?>" data-type="<?= $dt['Id'] ?>">
                            <img src="images/<?= $img ?>" alt="">
                            <span><?= htmlspecialchars($dt['Name']) ?></span>
                            <div class="check">✔</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </form>
        </div>

        <!-- ПРАВАЯ ЧАСТЬ -->
        <div class="right-panel">
            <div class="delivery-photo">
                <img src="images/dostav.jpg" alt="">
            </div>

            <div class="buttons-group">
                <a href="karta.php" class="btn">Отследить заказ</a>
                <button class="btn primary" onclick="submitOrder()">Заказать доставку</button>
                <a href="tekuch.php" class="btn">Текущие доставки</a>
                <a href="vse_dostav.php" class="btn">Все доставки</a>
            </div>
        </div>
    </div>

    <!-- О НАС -->
    <div class="about-section">
        <h2 class="about-title">Почему выбирают DeerGo?</h2>

        <div class="about-cards">
            <div class="about-card">
                <img src="images/fast.jpg" alt="">
                <h3>Быстро</h3>
                <p>Доставка в течение нескольких часов</p>
            </div>

            <div class="about-card">
                <img src="images/safe.jpg" alt="">
                <h3>Надёжно</h3>
                <p>Гарантия безопасности груза</p>
            </div>

            <div class="about-card">
                <img src="images/money.jpg" alt="">
                <h3>Выгодно</h3>
                <p>Честные цены без переплат</p>
            </div>
        </div>
    </div>

</main>

<script>
document.querySelectorAll('#size-group .card').forEach(card => {
    card.onclick = () => {
        document.querySelectorAll('#size-group .card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        weightVal.value = card.dataset.weight;
    };
});

document.querySelectorAll('#type-group .card').forEach(card => {
    card.onclick = () => {
        document.querySelectorAll('#type-group .card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        typeIdVal.value = card.dataset.type;
    };
});

function submitOrder() {
    var from = sanitize(document.getElementById('fromInput').value.trim());
    var to   = sanitize(document.getElementById('toInput').value.trim());
    if (!from || !to) {
        alert('Введите адреса');
        return;
    }
    document.getElementById('fromInput').value = from;
    document.getElementById('toInput').value   = to;
    document.getElementById('orderForm').submit();
}
</script>
<script src="scripts/sanitize.js"></script>
</body>
</html>