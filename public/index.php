<?php
session_start();
// Если не залогинен — на страницу входа
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
    <title>Главная — DeerGo</title>
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

        <!-- ПРИВЕТСТВИЕ -->
        <div class="page-intro">
            <h1 class="page-title">Быстрая доставка по городу</h1>
            <p class="page-subtitle">Отправьте посылку в несколько кликов — курьер заберёт её и доставит в нужное место</p>
        </div>

        <div class="top-row">
            <div class="left-panel">
                <div class="inputs-row">
                    <div class="input-box">
                        <input type="text" id="addr_from" placeholder="Откуда">
                        <button class="pin-btn">
                            <img src="images/otkuda.png" alt="pin">
                        </button>
                    </div>
                    <div class="input-box">
                        <input type="text" id="addr_to" placeholder="Куда">
                        <button class="pin-btn">
                            <img src="images/otkuda.png" alt="pin">
                        </button>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Размер доставки</div>
                    <div class="cards-row" id="size-group">
                        <div class="card active" data-weight="10"><span>до 10 кг</span><div class="check">✔</div></div>
                        <div class="card" data-weight="20"><span>до 20 кг</span><div class="check">✔</div></div>
                        <div class="card" data-weight="50"><span>до 50 кг</span><div class="check">✔</div></div>
                        <div class="card" data-weight="100"><span>до 100 кг</span><div class="check">✔</div></div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Вид доставки</div>
                    <div class="cards-row" id="type-group">
                        <div class="card" data-type="1" data-label="Курьер">
                            <span>Курьер</span>
                            <img src="images/courier.png" alt="Курьер">
                            <div class="check">✔</div>
                        </div>
                        <div class="card active" data-type="2" data-label="Легковая">
                            <span>Легковая</span>
                            <img src="images/car.png" alt="Легковая">
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-type="3" data-label="Фургон">
                            <span>Фургон</span>
                            <img src="images/gruzov.png" alt="Грузовая">
                            <div class="check">✔</div>
                        </div>
                        <div class="card" data-type="4" data-label="Грузовая">
                            <span>Грузовая</span>
                            <img src="images/bolsh.png" alt="Большая">
                            <div class="check">✔</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="delivery-photo">
                    <img src="images/dostav.jpg" alt="Доставка">
                </div>
                <div class="buttons-group">
                    <a href="track.php" class="btn-track">Отследить заказ</a>
                    <a href="oform.php" class="btn-order" id="btn-order">Заказать доставку</a>
                </div>
                <div class="buttons-group">
                    <a href="tekuch.php" class="btn-track">Текущие доставки</a>
                    <a href="vse_dostav.php" class="btn-order">Все доставки</a>
                </div>
            </div>
        </div>

    </main>

    <script>
    // Выбор карточки (один активный в группе)
    function setupGroup(groupId) {
        const group = document.getElementById(groupId);
        if (!group) return;
        group.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', () => {
                group.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            });
        });
    }

    setupGroup('size-group');
    setupGroup('type-group');

    // При нажатии «Заказать доставку» сохраняем выбор в sessionStorage
    document.getElementById('btn-order').addEventListener('click', function(e) {
        const weight = document.querySelector('#size-group .card.active')?.dataset.weight || '10';
        const typeId = document.querySelector('#type-group .card.active')?.dataset.type || '2';
        const typeLabel = document.querySelector('#type-group .card.active')?.dataset.label || 'Легковая';
        const addrFrom = document.getElementById('addr_from').value;
        const addrTo = document.getElementById('addr_to').value;

        sessionStorage.setItem('weight', weight);
        sessionStorage.setItem('typeId', typeId);
        sessionStorage.setItem('typeLabel', typeLabel);
        sessionStorage.setItem('addrFrom', addrFrom);
        sessionStorage.setItem('addrTo', addrTo);
    });
    </script>

</body>
</html>