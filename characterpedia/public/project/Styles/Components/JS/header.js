class MyHeader extends HTMLElement {
  connectedCallback() {
    const username = this.getAttribute('username') || 'Гость';
    
    this.innerHTML = `
    <header class="main-header">
      <div class="header-content">
        <div class="logo">🎬 CHARACTERPEDIA</div>
        <div class="user-info">
          <span>Добро пожаловать, 
            <span class="user-name">${username}</span>!
          </span>
          <a href="logout.php" class="logout-btn">Выйти</a>
        </div>
      </div>
    </header>
    `;
  }
}

customElements.define('my-header', MyHeader);