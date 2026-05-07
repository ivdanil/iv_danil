<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$character_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($character_id <= 0) {
    header('Location: heroes.php');
    exit;
}

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
    WHERE c.CharacterID = $character_id
";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: heroes.php');
    exit;
}

$character = mysqli_fetch_assoc($result);

if (!empty($character['ImageURL']) && $character['ImageURL'] !== null) {
    $imageData = $character['ImageURL'];
    $character['ImageData'] = 'data:image/jpeg;base64,' . base64_encode($imageData);
} else {
    $character['ImageData'] = null;
}

$relations_query = "
    SELECT cr.*, 
           c1.Name as FromName,
           c2.Name as ToName
    FROM `CharacterRelations` cr
    LEFT JOIN `Characters` c1 ON cr.CharacterFromID = c1.CharacterID
    LEFT JOIN `Characters` c2 ON cr.CharacterToID = c2.CharacterID
    WHERE cr.CharacterFromID = $character_id OR cr.CharacterToID = $character_id
";
$relations_result = mysqli_query($conn, $relations_query);
$relations = [];
if ($relations_result && mysqli_num_rows($relations_result) > 0) {
    while ($row = mysqli_fetch_assoc($relations_result)) {
        $relations[] = $row;
    }
}

$comments_query = "
    SELECT co.*, u.Username
    FROM `Comments` co
    LEFT JOIN `Users` u ON co.UserID = u.UserID
    WHERE co.CharacterID = $character_id AND co.IsApproved = 1
    ORDER BY co.CreatedDate DESC
";
$comments_result = mysqli_query($conn, $comments_query);
$comments = [];
if ($comments_result && mysqli_num_rows($comments_result) > 0) {
    while ($row = mysqli_fetch_assoc($comments_result)) {
        $comments[] = $row;
    }
}

$works_query = "
    SELECT w.*
    FROM `Works` w
    JOIN `Characters` c ON w.WorkID = c.WorkID
    WHERE c.CharacterID = $character_id
";
$works_result = mysqli_query($conn, $works_query);
$works = [];
if ($works_result && mysqli_num_rows($works_result) > 0) {
    while ($row = mysqli_fetch_assoc($works_result)) {
        $works[] = $row;
    }
}

mysqli_close($conn);
$is_favorite = false;
$conn2 = mysqli_connect('127.0.1.30', 'root', '', 'IVANOV_DB', 3306);
if ($conn2) {
    mysqli_set_charset($conn2, 'utf8');
    $uid = (int)$_SESSION['user_id'];
    $fav_res = mysqli_query($conn2, "SELECT FavoriteID FROM Favorites WHERE UserID = $uid AND CharacterID = $character_id");
    $is_favorite = ($fav_res && mysqli_num_rows($fav_res) > 0);
    mysqli_close($conn2);
}

$character_type = $character['CharacterType'] ?? '';

if ($character_type == 'Герой') {
    $current_page = 'heroes';
} elseif ($character_type == 'Злодей') {
    $current_page = 'villains';
} else {
    $current_page = 'main';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($character['Name']); ?> - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../Styles/Global/reset.css">
    <link rel="stylesheet" href="../Styles/Pages/main.css">
    <link rel="stylesheet" href="../Styles/Pages/character.css">
    <link rel="stylesheet" href="../Components/header.css">
    <link rel="stylesheet" href="../Components/footer.css">
    <link rel="stylesheet" href="../Components/nav.css">
    <style>
        .fav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #ff6b6b;
            border-radius: 10px;
            background: transparent;
            color: #ff6b6b;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .fav-btn:hover {
            background: #ff6b6b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,107,107,0.3);
        }
        .fav-btn--active {
            background: #ff6b6b;
            color: white;
        }
        .fav-btn--active:hover {
            background: #ff5252;
            border-color: #ff5252;
        }
        .fav-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #333;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            font-size: 0.95rem;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 9999;
            pointer-events: none;
        }
        .fav-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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
            <a href="main.php" <?php echo ($current_page == 'main') ? 'class="active"' : ''; ?>>Главная</a>
            <a href="heroes.php" <?php echo ($current_page == 'heroes') ? 'class="active"' : ''; ?>>Герои</a>
            <a href="villains.php" <?php echo ($current_page == 'villains') ? 'class="active"' : ''; ?>>Злодеи</a>
            <a href="profile.php">Кабинет</a>
        </div>
    </nav>
    
    <section class="page-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?php echo $character['ImageData'] ?? 'https://via.placeholder.com/1920x400?text=' . urlencode($character['Name']); ?>') center/cover no-repeat;">
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($character['Name']); ?></h1>
            <?php if (!empty($character['FirstAppearance'])): ?>
                <p class="hero-quote">Первое появление: <?php echo htmlspecialchars($character['FirstAppearance']); ?></p>
            <?php endif; ?>
        </div>
    </section>
    
    <main class="character-main">
        <div class="container">
            <div class="character-grid">
                <aside class="character-sidebar">
                    <div class="character-image-box">
                        <?php if (!empty($character['ImageData'])): ?>
                            <img src="<?php echo $character['ImageData']; ?>" alt="<?php echo htmlspecialchars($character['Name']); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x500?text=<?php echo urlencode($character['Name']); ?>" alt="<?php echo htmlspecialchars($character['Name']); ?>">
                        <?php endif; ?>
                    </div>
                    

                    <!-- Кнопка избранного -->
                    <button
                        id="fav-btn"
                        class="fav-btn <?php echo $is_favorite ? 'fav-btn--active' : ''; ?>"
                        onclick="toggleFavorite(<?php echo $character_id; ?>)"
                    >
                        <span id="fav-icon"><?php echo $is_favorite ? '' : ''; ?></span>
                        <span id="fav-text"><?php echo $is_favorite ? 'В избранном' : 'В избранное'; ?></span>
                    </button>
                    <div class="character-info-box">
                        <h3>Основная информация</h3>
                        <ul class="info-list">
                            <li><strong>Вселенная:</strong> <?php echo htmlspecialchars($character['UniverseName'] ?? 'Неизвестно'); ?></li>
                            <li><strong>Тип:</strong> <?php echo htmlspecialchars($character['CharacterType'] ?? 'Неизвестно'); ?></li>
                            <?php if (!empty($character['FirstAppearance'])): ?>
                                <li><strong>Первое появление:</strong> <?php echo htmlspecialchars($character['FirstAppearance']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($character['Gender'])): ?>
                                <li><strong>Пол:</strong> <?php echo htmlspecialchars($character['Gender']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($character['Status'])): ?>
                                <li><strong>Статус:</strong> <?php echo htmlspecialchars($character['Status']); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </aside>
                
                <div class="character-content">
                    <div class="content-section">
                        <h2>Биография</h2>
                        <p><?php echo nl2br(htmlspecialchars($character['Biography'] ?? 'Описание отсутствует')); ?></p>
                    </div>

                    <?php if (!empty($relations)): ?>
                    <div class="content-section">
                        <h2>Связи с другими персонажами</h2>
                        <div class="relations-list">
                            <?php foreach ($relations as $relation): ?>
                                <div class="relation-item">
                                    <div class="relation-type"><?php echo htmlspecialchars($relation['RelationType'] ?? 'Связь'); ?></div>
                                    <div class="relation-name">
                                        <?php 
                                        if ($relation['CharacterFromID'] == $character_id) {
                                            echo htmlspecialchars($relation['ToName']);
                                        } else {
                                            echo htmlspecialchars($relation['FromName']);
                                        }
                                        ?>
                                    </div>
                                    <?php if (!empty($relation['Description'])): ?>
                                        <div class="relation-desc"><?php echo htmlspecialchars($relation['Description']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($works)): ?>
                    <div class="content-section">
                        <h2>Появления в произведениях</h2>
                        <div class="movies-list">
                            <?php foreach ($works as $work): ?>
                                <div class="movie-item">
                                    <?php echo htmlspecialchars($work['Title'] ?? $work['Name'] ?? 'Произведение'); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($comments)): ?>
                    <div class="content-section">
                        <h2>Комментарии</h2>
                        <div class="comments-list">
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-author"><?php echo htmlspecialchars($comment['Username'] ?? 'Аноним'); ?></div>
                                    <div class="comment-date"><?php echo htmlspecialchars($comment['CreatedDate'] ?? ''); ?></div>
                                    <div class="comment-text"><?php echo htmlspecialchars($comment['CommentText'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="navigation-buttons">
                        <a href="<?php echo $current_page . '.php'; ?>" class="btn-back">← Вернуться к <?php echo ($current_page == 'heroes') ? 'героям' : (($current_page == 'villains') ? 'злодеям' : 'главной'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
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

    <div id="fav-toast" class="fav-toast"></div>

    <script>
    function toggleFavorite(characterId) {
        var btn  = document.getElementById('fav-btn');
        var icon = document.getElementById('fav-icon');
        var text = document.getElementById('fav-text');

        btn.disabled = true;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../api/favorite.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            btn.disabled = false;
            var data;
            try { data = JSON.parse(xhr.responseText); } catch(e) { return; }

            if (data.success) {
                if (data.action === 'added') {
                    icon.textContent = '';
                    text.textContent = 'В избранном';
                    btn.classList.add('fav-btn--active');
                    showToast('Добавлено в избранное ');
                } else {
                    icon.textContent = '';
                    text.textContent = 'В избранное';
                    btn.classList.remove('fav-btn--active');
                    showToast('Убрано из избранного');
                }
            }
        };

        xhr.onerror = function () { btn.disabled = false; };
        xhr.send('character_id=' + characterId);
    }

    function showToast(msg) {
        var toast = document.getElementById('fav-toast');
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(function() { toast.classList.remove('show'); }, 2500);
    }
    </script>

</body>
</html>