function showDetails(name) {
    alert(`Подробнее о ${name}`);
}

document.addEventListener('DOMContentLoaded', function() {
    
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.main-nav a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation.split('/').pop()) {
            link.classList.add('active');
        }
    });
});