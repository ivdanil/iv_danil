<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: signUp.php');
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
    WHERE c.CharacterType = 'Злодей'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Ошибка запроса: " . mysqli_error($conn));
}

$villains = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Преобразуем BLOB изображение в base64
        if (!empty($row['ImageURL']) && $row['ImageURL'] !== null) {
            $imageData = $row['ImageURL'];
            $row['ImageData'] = 'data:image/jpeg;base64,' . base64_encode($imageData);
        } else {
            $row['ImageData'] = null;
        }
        $villains[] = $row;
    }
}

mysqli_close($conn);

$current_page = 'villains';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Злодеи - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/main.css">
    <link rel="stylesheet" href="../../Styles/Pages/villains.css">
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
            <a href="heroes.php">Герои</a>
            <a href="villains.php" class="active">Злодеи</a>
        </div>
    </nav>
    <section class="page-hero villains-hero">
        <div class="hero-content">
            <h1>Культовые злодеи</h1>
        </div>
    </section>
    <section class="villains-section">
        <div class="container">
            <div class="villains-grid">
                <?php if (!empty($villains)): ?>
                    <?php foreach ($villains as $villain): ?>
                        <a href="character.php?id=<?php echo $villain['CharacterID']; ?>" style="text-decoration: none;">
                            <div class="villain-card">
                                <div class="villain-image">
                                    <?php if (!empty($villain['ImageData'])): ?>
                                        <img src="<?php echo $villain['ImageData']; ?>" 
                                             alt="<?php echo htmlspecialchars($villain['Name']); ?>" 
                                             loading="lazy">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300x200?text=<?php echo urlencode($villain['Name']); ?>" 
                                             alt="<?php echo htmlspecialchars($villain['Name']); ?>" 
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <div class="villain-info">
                                    <h3><?php echo htmlspecialchars($villain['Name']); ?></h3>
                                    <p class="villain-description"><?php echo htmlspecialchars($villain['Biography'] ?? 'Описание отсутствует'); ?></p>
                                    <div class="villain-meta">
                                        <span class="villain-universe"><?php echo htmlspecialchars($villain['UniverseName'] ?? 'Неизвестно'); ?></span>
                                        <span class="villain-type"><?php echo htmlspecialchars($villain['CharacterType'] ?? 'villain'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Нет данных о злодеях</p>
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