<?php
require_once 'includes/auth_check.php'; // Убрали ../, так как includes в той же папке Pages
$current_page = 'villains';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Злодеи - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../Styles/Pages/main.css">
    <link rel="stylesheet" href="../Styles/Pages/villains.css">
    <link rel="stylesheet" href="../Components/cards.css">
    <link rel="stylesheet" href="../Components/header.css">
    <link rel="stylesheet" href="../Components/footer.css">
    <link rel="stylesheet" href="../Components/nav.css">
    <script src="../Scripts/main.js" defer></script>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <div class="logo">CHARACTERPEDIA</div>
            <div class="user-info">
                <span>Добро пожаловать, <span class="user-name"><?php echo htmlspecialchars($username); ?></span>!</span>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
    </header>

    <!-- Nav -->
    <nav class="main-nav">
        <div class="nav-container">
            <a href="main.php">Главная</a>
            <a href="heroes.php">Герои</a>
            <a href="villains.php" class="active">Злодеи</a>
            <a href="#films">Фильмы</a>
        </div>
    </nav>

    <!-- Hero секция -->
    <section class="page-hero villains-hero">
        <div class="hero-content">
            <h1>Культовые злодеи</h1>
        </div>
    </section>

    <!-- Злодеи -->
    <section class="villains-section">
        <div class="container">
            <div class="villains-grid">
                <!-- Дарт Вейдер -->
                <div class="character-card villain-card">
                    <div class="character-image">
                        <img src="https://cdn.fishki.net/upload/post/2016/12/27/2176615/tn/ac9b29847547e1863d7b77459a63d1d2.jpg" 
                             alt="Дарт Вейдер" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Дарт Вейдер</h3>
                        <p class="character-description">Бывший Энакин Скайуокер, рыцарь-джедай, павший на Тёмную сторону Силы.</p>
                        <div class="character-meta">
                            <span class="character-universe">Звёздные Войны</span>
                            <span class="character-type">Ситх</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Дарт Вейдер')">Подробнее</button>
                    </div>
                </div>

                <!-- Джокер -->
                <div class="character-card villain-card">
                    <div class="character-image">
                        <img src="https://gamebomb.ru/files/galleries/001/9/9b/405115.jpg" 
                             alt="Джокер" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Джокер</h3>
                        <p class="character-description">Загадочный преступник, олицетворяющий хаос и анархию.</p>
                        <div class="character-meta">
                            <span class="character-universe">DC Comics</span>
                            <span class="character-type">Криминальный гений</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Джокер')">Подробнее</button>
                    </div>
                </div>

                <!-- Танос -->
                <div class="character-card villain-card">
                    <div class="character-image">
                        <img src="https://upload.wikimedia.org/wikipedia/ru/7/7b/Josh_Brolin_as_Thanos.jpeg" 
                             alt="Танос" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Танос</h3>
                        <p class="character-description">Бессмертный титан, стремящийся восстановить баланс во вселенной.</p>
                        <div class="character-meta">
                            <span class="character-universe">Marvel Comics</span>
                            <span class="character-type">Безумный Титан</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Танос')">Подробнее</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>О проекте</h3>
                <p>Энциклопедия культовых персонажей - это собрание самых известных персонажей.</p>
            </div>
            
            <div class="footer-section">
                <h3>Быстрые ссылки</h3>
                <ul class="footer-links">
                    <li><a href="main.php">Главная</a></li>
                    <li><a href="heroes.php">Герои</a></li>
                    <li><a href="villains.php">Злодеи</a></li>
                    <li><a href="#films">Фильмы</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Контакты</h3>
                <p>📧 info@characterpedia.ru</p>
                <p>📞 +7 (964) 426-79-90</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2025 Энциклопедия культовых персонажей. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>