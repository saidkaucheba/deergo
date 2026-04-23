<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeerGo — Для курьеров</title>
    <link rel="stylesheet" href="css/fonts.css"> 
    <link rel="stylesheet" href="css/cour.css">
</head>
<body>

<!-- ХЕДЕР (как на главной) -->
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
            <a href="cour.php"  class="tab-option active">Для курьеров</a>
        </div>
        <a href="profil.php" class="profile-btn">Профиль</a>
    </nav>
</header>

<main class="main">
    <h1 class="page-title">Стань курьером DeerGo</h1>
    <p class="page-sub">Работай в удобное время и зарабатывай больше.</p>

    <!-- ВЫБОР ТРАНСПОРТА -->
    <div class="section">
        <div class="section-title">Выберите, кем работать</div>
        <div class="transport-row" id="transport-group">
            <div class="transport-card active" data-val="Курьер">
                <img src="images/courier.png" alt="Курьер">
                <span>Курьер</span>
                <div class="transport-check">✔</div>
            </div>
            <div class="transport-card" data-val="Легковая">
                <img src="images/car.png" alt="Легковая">
                <span>Водитель легковой машины</span>
                <div class="transport-check">✔</div>
            </div>
            <div class="transport-card" data-val="Фургон">
                <img src="images/gruzov.png" alt="Фургон">
                <span>Водитель фургона</span>
                <div class="transport-check">✔</div>
            </div>
            <div class="transport-card" data-val="Грузовая">
                <img src="images/bolsh.png" alt="Грузовая">
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
                <!-- Замените на ваши файлы: images/icon_money.png и т.д. -->
                <div class="benefit-icon">
                    <img src="images/icon_money.png" alt="Доход" onerror="this.style.display='none';this.parentNode.innerHTML='💰'">
                </div>
                <h3>Высокий доход</h3>
                <p>Зарабатывай от 5000 ₽ в день. Выплаты каждую неделю</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <img src="images/icon_schedule.png" alt="График" onerror="this.style.display='none';this.parentNode.innerHTML='🕐'">
                </div>
                <h3>Гибкий график</h3>
                <p>Работай когда удобно. Никаких штрафов за отказы</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <img src="images/icon_home.png" alt="Рядом" onerror="this.style.display='none';this.parentNode.innerHTML='🏠'">
                </div>
                <h3>Рядом с домом</h3>
                <p>Выбирай заказы в своём районе. Не нужно ехать далеко</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <img src="images/icon_app.png" alt="Приложение" onerror="this.style.display='none';this.parentNode.innerHTML='📱'">
                </div>
                <h3>Удобное приложение</h3>
                <p>Всё в телефоне: заказы, маршруты, выплаты</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <img src="images/icon_insurance.png" alt="Страховка" onerror="this.style.display='none';this.parentNode.innerHTML='🛡️'">
                </div>
                <h3>Страховка</h3>
                <p>Бесплатная страховка на время выполнения заказа</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <img src="images/icon_bonus.png" alt="Бонусы" onerror="this.style.display='none';this.parentNode.innerHTML='🎁'">
                </div>
                <h3>Бонусы и акции</h3>
                <p>Дополнительные выплаты за активность и часы пик</p>
            </div>
        </div>
    </div>

    <!-- РАСПИСАНИЕ -->
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
                <input type="tel" id="phone" placeholder="Номер телефона">
            </div>
        </div>
    </div>

    <div class="submit-section">
        <button class="btn-submit" onclick="submitApplication()">Отправить заявку</button>
    </div>

</main>

<script>
// ===== ПОЛЗУНОК ТРАНСПОРТ =====
document.querySelectorAll('#transport-group .transport-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('#transport-group .transport-card').forEach(function(c) {
            c.classList.remove('active');
        });
        card.classList.add('active');
    });
});

// ===== ПОЛЗУНОК РАСПИСАНИЕ =====
document.querySelectorAll('#schedule-group .schedule-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('#schedule-group .schedule-card').forEach(function(c) {
            c.classList.remove('active');
        });
        card.classList.add('active');
    });
});

// ===== ОТПРАВКА ЗАЯВКИ =====
function submitApplication() {
    var firstname = sanitize(document.getElementById('firstname').value.trim());
    var lastname  = sanitize(document.getElementById('lastname').value.trim());
    var phone     = sanitize(document.getElementById('phone').value.trim());
    var transport = document.querySelector('#transport-group .transport-card.active span').textContent;
    var schedule  = document.querySelector('#schedule-group .schedule-card.active').textContent;

    if (!firstname || !phone) {
        alert('Пожалуйста, заполните имя и номер телефона.');
        return;
    }

    alert('Заявка отправлена!\n\nИмя: ' + firstname + '\nФамилия: ' + lastname +
          '\nТелефон: ' + phone + '\nТранспорт: ' + transport + '\nРасписание: ' + schedule);
}
</script>
<script src="scripts/sanitize.js"></script>
</body>
</html>