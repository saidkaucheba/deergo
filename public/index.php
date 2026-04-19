<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['delivery_from']   = $_POST['from']    ?? '';
    $_SESSION['delivery_to']     = $_POST['to']      ?? '';
    $_SESSION['delivery_weight'] = $_POST['weight']  ?? 10;
    $_SESSION['delivery_type']   = $_POST['type_id'] ?? 1;
    header('Location: oform.php');
    exit;
}
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
            <a href="cour.php"  class="tab-option">Для курьеров</a>
        </div>
        <a href="profil.php" class="profile-btn">Профиль</a>
    </nav>
</header>

<main class="main">

    <p class="page-greeting">Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</p>
    <h1 class="page-title">Быстрая доставка по всему городу</h1>
    <p class="page-sub">Выберите адреса и параметры — мы доставим быстро и бережно.</p>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'empty_address'): ?>
        <p class="error-msg">⚠️ Пожалуйста, введите адреса «Откуда» и «Куда» перед оформлением.</p>
    <?php endif; ?>

    <div class="top-row">
        <div class="left-panel">
            <form method="POST" id="orderForm">
                <div class="inputs-row">
                    <div class="input-box">
                        <input type="text" name="from" id="fromInput" placeholder="Откуда" required>
                        <button type="button" class="pin-btn">
                            <img src="images/otkuda.png" alt="pin">
                        </button>
                    </div>
                    <div class="input-box">
                        <input type="text" name="to" id="toInput" placeholder="Куда" required>
                        <button type="button" class="pin-btn">
                            <img src="images/otkuda.png" alt="pin">
                        </button>
                    </div>
                </div>

                <input type="hidden" name="weight"  id="weightVal" value="10">
                <input type="hidden" name="type_id" id="typeIdVal" value="1">

                <div class="section">
                    <div class="section-title">Размер доставки</div>
                    <div class="cards-row" id="size-group">
                        <div class="card active" data-weight="10">
                            <span>до 10 кг</span>
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-weight="20">
                            <span>до 20 кг</span>
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-weight="50">
                            <span>до 50 кг</span>
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-weight="100">
                            <span>до 100 кг</span>
                            <div class="check">✔</div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Вид доставки</div>
                    <div class="cards-row" id="type-group">
                        <div class="card" data-type="1">
                            <span>Курьер</span>
                            <img src="images/courier.png" alt="Курьер">
                            <div class="check">✔</div>
                        </div>
                        <div class="card active" data-type="2">
                            <span>Легковая</span>
                            <img src="images/car.png" alt="Легковая">
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-type="3">
                            <span>Фургон</span>
                            <img src="images/gruzov.png" alt="Грузовая">
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-type="4">
                            <span>Грузовая</span>
                            <img src="images/bolsh.png" alt="Большая">
                            <div class="check">✔</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="right-panel">
            <div class="delivery-photo">
                <img src="images/dostav.jpg" alt="Доставка">
            </div>
            <div class="buttons-group">
                <a href="karta.php"      class="btn-track">Отследить заказ</a>
                <button class="btn-order" onclick="submitOrder()">Заказать доставку</button>
            </div>
            <div class="buttons-group">
                <a href="tekuch.php"     class="btn-track">Текущие доставки</a>
                <a href="vse_dostav.php" class="btn-order">Все доставки</a>
            </div>
        </div>
    </div>

    <div class="about-section">
        <h2 class="about-title">Почему выбирают DeerGo?</h2>
        <div class="about-cards">
            <div class="about-card">
                <div class="about-icon">🚀</div>
                <h3>Быстро</h3>
                <p>Доставляем в течение нескольких часов по всему городу</p>
            </div>
            <div class="about-card">
                <div class="about-icon">🛡️</div>
                <h3>Надёжно</h3>
                <p>Ваш груз в безопасности — мы несём ответственность за каждый заказ</p>
            </div>
            <div class="about-card">
                <div class="about-icon">💰</div>
                <h3>Выгодно</h3>
                <p>Честные цены без скрытых платежей и наценок</p>
            </div>
        </div>
    </div>

</main>

<script>
document.querySelectorAll('#size-group .card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('#size-group .card').forEach(function(c) { c.classList.remove('active'); });
        card.classList.add('active');
        document.getElementById('weightVal').value = card.dataset.weight;
    });
});

document.querySelectorAll('#type-group .card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('#type-group .card').forEach(function(c) { c.classList.remove('active'); });
        card.classList.add('active');
        document.getElementById('typeIdVal').value = card.dataset.type;
    });
});

function submitOrder() {
    var from = document.getElementById('fromInput').value.trim();
    var to   = document.getElementById('toInput').value.trim();
    if (!from || !to) {
        alert('Пожалуйста, введите адреса «Откуда» и «Куда».');
        return;
    }
    document.getElementById('orderForm').submit();
}
</script>

</body>
</html>