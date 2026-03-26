class MyFooter extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
    <footer class="main-footer">
      <div class="footer-content">
        <div class="footer-section">
          <h3>О проекте</h3>
          <p>Энциклопедия культовых персонажей - это собрание самых известных и значимых персонажей из мира кино, игр и комиксов.</p>
        </div>
        
        <div class="footer-section">
          <h3>Быстрые ссылки</h3>
          <ul class="footer-links">
            <li><a href="main.php">Главная</a></li>
            <li><a href="heroes.php">Герои</a></li>
            <li><a href="villains.php">Злодеи</a></li>
            <li><a href="#movies">Фильмы</a></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Контакты</h3>
          <p>📧 info@characterpedia.ru</p>
          <p>📞 +7 (964) 426-79-90</p>
          <div class="social-links">
            <a href="#" title="ВКонтакте">VK</a>
            <a href="#" title="Telegram">TG</a>
            <a href="#" title="Rutube">RT</a>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 Энциклопедия культовых персонажей. Все права защищены.</p>
      </div>
    </footer>
    `;
  }
}

customElements.define('my-footer', MyFooter);