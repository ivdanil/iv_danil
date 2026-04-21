class CharacterCard extends HTMLElement {
  connectedCallback() {
    const image = this.getAttribute('image') || 'https://via.placeholder.com/300x200';
    const name = this.getAttribute('name') || 'Персонаж';
    const description = this.getAttribute('description') || 'Описание персонажа';
    const type = this.getAttribute('type') || 'character';
    
    this.innerHTML = `
    <div class="card ${type}">
      <div class="card-image">
        <img src="${image}" alt="${name}">
      </div>
      <div class="card-content">
        <h3>${name}</h3>
        <p>${description}</p>
        <button class="card-btn" onclick="alert('Подробнее о ${name}')">Подробнее</button>
      </div>
    </div>
    `;
  }
}

customElements.define('character-card', CharacterCard);