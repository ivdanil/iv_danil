<?php
require_once 'includes/auth_check.php'; // Убрали ../, так как includes в той же папке Pages
$current_page = 'heroes';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Герои - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../Styles/Pages/main.css">
    <link rel="stylesheet" href="../Styles/Pages/heroes.css">
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
            <a href="heroes.php" class="active">Герои</a>
            <a href="villains.php">Злодеи</a>
            <a href="#films">Фильмы</a>
        </div>
    </nav>

    <!-- Hero секция -->
    <section class="page-hero heroes-hero">
        <div class="hero-content">
            <h1>Культовые герои</h1>
        </div>
    </section>

    <!-- Герои -->
    <section class="heroes-section">
        <div class="container">
            <div class="heroes-grid">
                <!-- Человек-паук -->
                <div class="character-card hero-card">
                    <div class="character-image">
                        <img src="https://i.bigenc.ru/resizer/resize?sign=OyQueVxbzcpJ4dirKjERbg&filename=vault/84d5593080c47a4910358619baee0adb.webp&width=1200" 
                             alt="Человек-паук" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Человек-паук</h3>
                        <p class="character-description">Питер Паркер - обычный подросток, получивший суперспособности после укуса радиоактивного паука.</p>
                        <div class="character-meta">
                            <span class="character-universe">Marvel Comics</span>
                            <span class="character-type">Супергерой</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Человек-паук')">Подробнее</button>
                    </div>
                </div>

                <!-- Бэтмен -->
                <div class="character-card hero-card">
                    <div class="character-image">
                        <img src="https://cdn1.epicgames.com/undefined/offer/batman-arkham-knight_promo-2048x1152-ed2be22b3f24f446534b90b122ed560d.jpg" 
                             alt="Бэтмен" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Бэтмен</h3>
                        <p class="character-description">Брюс Уэйн, миллиардер, который после гибели родителей поклялся бороться с преступностью в Готэм-сити.</p>
                        <div class="character-meta">
                            <span class="character-universe">DC Comics</span>
                            <span class="character-type">Супергерой</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Бэтмен')">Подробнее</button>
                    </div>
                </div>

                <!-- Люк Скайуокер -->
                <div class="character-card hero-card">
                    <div class="character-image">
                        <img src="https://citaty.info/files/portraits/lyuk-skaiuoker.jpg" 
                             alt="Люк Скайуокер" loading="lazy">
                    </div>
                    <div class="character-info">
                        <h3>Люк Скайуокер</h3>
                        <p class="character-description">Центральный персонаж «Звездных войн», джедай, сын Энакина Скайуокера и Падме Амидалы.</p>
                        <div class="character-meta">
                            <span class="character-universe">Lucasfilm</span>
                            <span class="character-type">Джедай</span>
                        </div>
                        <button class="card-btn" onclick="showDetails('Люк Скайуокер')">Подробнее</button>
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

    <script>
        function showDetails(name) {
            alert(`Подробнее о ${name}`);
        }
    </script>
</body>
</html>