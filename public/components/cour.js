class MyHeader extends HTMLElement {
    connectedCallback() {
        const path = window.location.pathname;
        const isMain = path.includes('index') || path.endsWith('/');
        const isCourier = path.includes('cour');

        this.innerHTML = `
        <header class="header">
            <div class="header-left">
                <div class="logo-img">
                    <img src="images/log.png" alt="логотип">
                </div>
                <span class="brand">DeerGo</span>
            </div>
            <nav class="nav-menu">
                <div class="tab-switch">
                    <a href="index.php" class="tab-option ${isMain ? 'active' : ''}">Главная</a>
                    <a href="cour.php" class="tab-option ${isCourier ? 'active' : ''}">Для курьеров</a>
                </div>
                <a href="profil.php" class="profile-btn">Профиль</a>
            </nav>
        </header>`;
    }
}

customElements.define('my-header', MyHeader);

function setupTransportSelect() {
    const cards = document.querySelectorAll('.transport-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
        });
    });
}

function setupScheduleSelect() {
    const cards = document.querySelectorAll('.schedule-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
        });
    });
}

function submitApplication() {
    const firstname = document.getElementById('firstname')?.value.trim() || '';
    const lastname = document.getElementById('lastname')?.value.trim() || '';
    const phone = document.getElementById('number')?.value.trim() || '';
    const transport = document.querySelector('.transport-card.active span')?.textContent || 'не выбран';
    const schedule = document.querySelector('.schedule-card.active')?.textContent || 'не выбрано';

    if (!firstname || !lastname) {
        alert('Пожалуйста, заполните имя и фамилию');
        return;
    }

    alert(`Заявка отправлена!\n\nИмя: ${firstname}\nФамилия: ${lastname}\nТелефон: ${phone}\nТранспорт: ${transport}\nРасписание: ${schedule}`);
}

document.addEventListener('DOMContentLoaded', () => {
    setupTransportSelect();
    setupScheduleSelect();

    const submitBtn = document.querySelector('.btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            submitApplication();
        });
    }
});