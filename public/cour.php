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
    <title>Для курьеров — DeerGo</title>
    <link rel="stylesheet" href="css/cour.css">
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
                <a href="cour.php" class="tab-option active">Для курьеров</a>
            </div>
            <a href="profil.php" class="profile-btn">Профиль</a>
        </nav>
    </header>

    <main class="main">
        <h1 class="page-title">Стань курьером DeerGo</h1>

        <!-- ВЫБОР ТРАНСПОРТА -->
        <div class="section">
            <div class="section-title">Выберите кем работать</div>
            <div class="transport-row" id="transport-group">
                <div class="transport-card active">
                    <img src="images/courier.png" alt="Курьер">
                    <span>Курьер</span>
                    <div class="transport-check">✔</div>
                </div>
                <div class="transport-card">
                    <img src="images/car.png" alt="Легковая">
                    <span>Водитель легковой машины</span>
                    <div class="transport-check">✔</div>
                </div>
                <div class="transport-card">
                    <img src="images/gruzov.png" alt="Грузовая">
                    <span>Водитель фургона</span>
                    <div class="transport-check">✔</div>
                </div>
                <div class="transport-card">
                    <img src="images/bolsh.png" alt="Большая">
                    <span>Водитель грузовой машины</span>
                    <div class="transport-check">✔</div>
                </div>
            </div>
        </div>

        <!-- ПРЕИМУЩЕСТВА -->
        <div class="section">
            <div class="section-title">Почему работать курьером удобно</div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_money.png" alt="Доход">
                    </div>
                    <h3>Высокий доход</h3>
                    <p>Зарабатывай от 5000 ₽ в день. Выплаты каждую неделю</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_schedule.png" alt="График">
                    </div>
                    <h3>Гибкий график</h3>
                    <p>Работай когда удобно. Никаких штрафов за отказы</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_home.png" alt="Рядом">
                    </div>
                    <h3>Рядом с домом</h3>
                    <p>Выбирай заказы в своём районе. Не нужно ехать далеко</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_app.png" alt="Приложение">
                    </div>
                    <h3>Удобное приложение</h3>
                    <p>Всё в телефоне: заказы, маршруты, выплаты</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_insurance.png" alt="Страховка">
                    </div>
                    <h3>Страховка</h3>
                    <p>Бесплатная страховка на время выполнения заказа</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <img src="images/benefit_bonus.png" alt="Бонусы">
                    </div>
                    <h3>Бонусы и акции</h3>
                    <p>Дополнительные выплаты за активность и часы пик</p>
                </div>
            </div>
        </div>

        <!-- ВЫБОР РАСПИСАНИЯ -->
        <div class="section">
            <div class="section-title">Выберите расписание</div>
            <div class="schedule-row" id="schedule-group">
                <div class="schedule-card active">Ежедневно</div>
                <div class="schedule-card">Любой формат</div>
                <div class="schedule-card">По будням</div>
            </div>
        </div>

        <!-- ФОРМА -->
        <div class="form-section">
            <div class="form-group">
                <div class="form-input">
                    <input type="text" id="firstname" placeholder="Имя">
                </div>
                <div class="form-input">
                    <input type="text" id="lastname" placeholder="Фамилия">
                </div>
                <div class="form-input">
                    <input type="text" id="phone" placeholder="Номер телефона">
                </div>
            </div>
        </div>

        <div class="submit-section">
            <button class="btn-submit" onclick="submitApplication()">Отправить заявку</button>
        </div>
    </main>

    <script>
    // Выбор карточки в группе (один активный)
    function setupGroup(groupId) {
        const group = document.getElementById(groupId);
        if (!group) return;
        group.querySelectorAll('.transport-card, .schedule-card').forEach(card => {
            card.addEventListener('click', () => {
                group.querySelectorAll('.transport-card, .schedule-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            });
        });
    }

    setupGroup('transport-group');
    setupGroup('schedule-group');

    function submitApplication() {
        const firstname = document.getElementById('firstname').value.trim();
        const lastname  = document.getElementById('lastname').value.trim();
        const phone     = document.getElementById('phone').value.trim();
        const transport = document.querySelector('#transport-group .active span')?.textContent || '';
        const schedule  = document.querySelector('#schedule-group .active')?.textContent || '';

        if (!firstname || !lastname) {
            alert('Пожалуйста, заполните имя и фамилию');
            return;
        }

        alert('Заявка отправлена!\n\nИмя: ' + firstname + '\nФамилия: ' + lastname + '\nТелефон: ' + phone + '\nТип: ' + transport + '\nРасписание: ' + schedule);
    }
    </script>

</body>
</html>