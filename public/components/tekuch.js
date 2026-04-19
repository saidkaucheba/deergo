class MyHeader extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
        <header class="header">
            <div class="header-left">
                <div class="logo-img">
                    <img src="images/log.png" alt="логотип">
                </div>
                <span class="brand">DeerGo</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-btn">Главное меню</a>
                <a href="vse_dostav.php" class="nav-btn">Все доставки</a>
                <a href="profil.php" class="nav-btn">Профиль</a>
            </nav>
        </header>`;
    }
}
customElements.define('my-header', MyHeader);