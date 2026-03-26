<?php
require_once 'includes/auth_check.php'; // Убрали ../, так как includes в той же папке Pages
$current_page = 'main';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../Styles/Pages/main.css">
    <link rel="stylesheet" href="../Components/cards.css">
    <link rel="stylesheet" href="../Components/header.css">
    <link rel="stylesheet" href="../Components/footer.css">
    <link rel="stylesheet" href="../Components/nav.css">
    <script src="../Scripts/main.js" defer></script>
</head>
<body>
    <!-- Header компонент -->
    <header class="main-header">
        <div class="header-content">
            <div class="logo">CHARACTERPEDIA</div>
            <div class="user-info">
                <span>Добро пожаловать, <span class="user-name"><?php echo htmlspecialchars($username); ?></span>!</span>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
    </header>

    <!-- Nav компонент -->
    <nav class="main-nav">
        <div class="nav-container">
            <a href="main.php" <?php echo $current_page === 'main' ? 'class="active"' : ''; ?>>Главная</a>
            <a href="heroes.php" <?php echo $current_page === 'heroes' ? 'class="active"' : ''; ?>>Герои</a>
            <a href="villains.php" <?php echo $current_page === 'villains' ? 'class="active"' : ''; ?>>Злодеи</a>
            <a href="#films">Фильмы</a>
        </div>
    </nav>

    <!-- Hero секция -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Погрузитесь в мир культовых персонажей</h1>
        </div>
    </section>

    <!-- Популярные персонажи -->
    <section class="popular-section">
        <h2>Популярные персонажи</h2>
        <div class="characters-grid">
            <!-- Карточка персонажа 1 -->
            <div class="character-card">
                <div class="character-image">
                    <img src="https://i.bigenc.ru/resizer/resize?sign=OyQueVxbzcpJ4dirKjERbg&filename=vault/84d5593080c47a4910358619baee0adb.webp&width=1200" 
                         alt="Человек-паук" loading="lazy">
                </div>
                <div class="character-info">
                    <h3>Человек-паук</h3>
                    <p class="character-description">Человек-паук (Питер Паркер) — это супергерой из комиксов Marvel, который получил свои способности после укуса генетически модифицированного паука.</p>
                    <button class="card-btn" onclick="showDetails('Человек-паук')">Подробнее</button>
                </div>
            </div>

            <!-- Карточка персонажа 2 -->
            <div class="character-card">
                <div class="character-image">
                    <img src="https://ic.pics.livejournal.com/66sean99/13114510/2041339/2041339_original.jpg" 
                         alt="Джек Воробей" loading="lazy">
                </div>
                <div class="character-info">
                    <h3>Джек Воробей</h3>
                    <p class="character-description">Джек Воробей — легендарный пират, авантюрист и главный герой франшизы «Пираты Карибского моря».</p>
                    <button class="card-btn" onclick="showDetails('Джек Воробей')">Подробнее</button>
                </div>
            </div>

            <!-- Карточка персонажа 3 -->
            <div class="character-card">
                <div class="character-image">
                    <img src="https://cdn.fishki.net/upload/post/2016/12/27/2176615/tn/ac9b29847547e1863d7b77459a63d1d2.jpg" 
                         alt="Дарт Вейдер" loading="lazy">
                </div>
                <div class="character-info">
                    <h3>Дарт Вейдер</h3>
                    <p class="character-description">Дарт Вейдер — центральный персонаж саги «Звездные войны», бывший рыцарь-джедай, перешедший на Тёмную сторону Силы.</p>
                    <button class="card-btn" onclick="showDetails('Дарт Вейдер')">Подробнее</button>
                </div>
            </div>

            <!-- Карточка персонажа 4 -->
            <div class="character-card">
                <div class="character-image">
                    <img src="https://cdn1.epicgames.com/undefined/offer/batman-arkham-knight_promo-2048x1152-ed2be22b3f24f446534b90b122ed560d.jpg" 
                         alt="Бэтмен" loading="lazy">
                </div>
                <div class="character-info">
                    <h3> Бэтмен</h3>
                    <p class="character-description">Бэтмен (Брюс Уэйн) — супергерой из комиксов DC, миллиардер, посвятивший жизнь борьбе с преступностью.</p>
                    <button class="card-btn" onclick="showDetails('Бэтмен')">Подробнее</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer компонент -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>О проекте</h3>
                <p>Энциклопедия культовых персонажей - это собрание самых известных персонажей из мира кино, игр и комиксов.</p>
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