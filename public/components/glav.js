class MyHeader extends HTMLElement {
    connectedCallback() {
        const path = window.location.pathname;
        const isMain = path.includes('index') || path.endsWith('/') || path.endsWith('index.php');
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

function setupSelectableGroup(groupId) {
    const group = document.getElementById(groupId);
    if (!group) return;
    const cards = group.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            cards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupSelectableGroup('size-group');
    setupSelectableGroup('type-group');
});