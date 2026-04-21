<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$host = '127.0.1.30';
$port = 3306;
$user = 'root';
$password = '';
$database = 'IVANOV_DB';

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

$query = "
    SELECT c.*, u.Name as UniverseName, ci.ImageURL 
    FROM `Characters` c
    LEFT JOIN `Universes` u ON c.UniverseID = u.UniverseID
    LEFT JOIN `CharacterImages` ci ON c.CharacterID = ci.CharacterID
    WHERE c.CharacterType = 'Герой'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Ошибка запроса: " . mysqli_error($conn));
}

$heroes = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['ImageURL']) && $row['ImageURL'] !== null) {
            $imageData = $row['ImageURL'];
            $row['ImageData'] = 'data:image/jpeg;base64,' . base64_encode($imageData);
        } else {
            $row['ImageData'] = null;
        }
        $heroes[] = $row;
    }
}

mysqli_close($conn);

$current_page = 'heroes';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Герои - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/main.css">
    <link rel="stylesheet" href="../../Styles/Pages/heroes.css">
    <link rel="stylesheet" href="../../Components/header.css">
    <link rel="stylesheet" href="../../Components/footer.css">
    <link rel="stylesheet" href="../../Components/nav.css">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">CHARACTERPEDIA</div>
            <div class="user-info">
                <span>Добро пожаловать, <span class="user-name"><?php echo htmlspecialchars($username); ?></span>!</span>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
    </header>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="main.php">Главная</a>
            <a href="heroes.php" class="active">Герои</a>
            <a href="villains.php">Злодеи</a>
        </div>
    </nav>
    <section class="page-hero heroes-hero">
        <div class="hero-content">
            <h1>Культовые герои</h1>
        </div>
    </section>
    <section class="heroes-section">
        <div class="container">
            <div class="heroes-grid">
                <?php if (!empty($heroes)): ?>
                    <?php foreach ($heroes as $hero): ?>
                        <a href="character.php?id=<?php echo $hero['CharacterID']; ?>" style="text-decoration: none;">
                            <div class="hero-card">
                                <div class="hero-image">
                                    <?php if (!empty($hero['ImageData'])): ?>
                                        <img src="<?php echo $hero['ImageData']; ?>" 
                                             alt="<?php echo htmlspecialchars($hero['Name']); ?>" 
                                             loading="lazy">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300x200?text=<?php echo urlencode($hero['Name']); ?>" 
                                             alt="<?php echo htmlspecialchars($hero['Name']); ?>" 
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <div class="hero-info">
                                    <h3><?php echo htmlspecialchars($hero['Name']); ?></h3>
                                    <p class="hero-description"><?php echo htmlspecialchars($hero['Biography'] ?? 'Описание отсутствует'); ?></p>
                                    <div class="hero-meta">
                                        <span class="hero-universe"><?php echo htmlspecialchars($hero['UniverseName'] ?? 'Неизвестно'); ?></span>
                                        <span class="hero-type"><?php echo htmlspecialchars($hero['CharacterType'] ?? 'unknown'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Нет данных о персонажах</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
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
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Контакты</h3>
                <p>📧 info@characterpedia.ru</p>
                <p>📞 +7 (964) 426-79-90</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2026 Энциклопедия культовых персонажей. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>