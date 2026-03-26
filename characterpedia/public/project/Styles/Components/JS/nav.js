class MyNav extends HTMLElement {
  connectedCallback() {
    const currentPage = this.getAttribute('current-page') || 'main';
    
    let mainClass = currentPage === 'main' ? 'style="background: #ff6b6b;"' : '';
    let heroesClass = currentPage === 'heroes' ? 'style="background: #ff6b6b;"' : '';
    let villainsClass = currentPage === 'villains' ? 'style="background: #ff6b6b;"' : '';
    
    this.innerHTML = `
    <nav class="main-nav">
      <div class="nav-container">
        <a href="main.php" ${mainClass}>🏠 Главная</a>
        <a href="heroes.php" ${heroesClass}>🏆 Герои</a>
        <a href="villains.php" ${villainsClass}>👹 Злодеи</a>
        <a href="#films">📚 Фильмы</a>
      </div>
    </nav>
    `;
  }
}

customElements.define('my-nav', MyNav);