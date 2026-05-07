<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$user_id  = (int)$_SESSION['user_id'];

$host     = '127.0.1.30';
$port     = 3306;
$user     = 'root';
$password = '';
$database = 'IVANOV_DB';

$conn = mysqli_connect($host, $user, $password, $database, $port);
if (!$conn) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');

$user_result = mysqli_query($conn, "
    SELECT Username, Email, Role, RegistrationDate
    FROM Users WHERE UserID = $user_id
");
$user_data = mysqli_fetch_assoc($user_result);

$favorites_result = mysqli_query($conn, "
    SELECT
        c.CharacterID,
        c.Name,
        c.CharacterType,
        c.Biography,
        c.FirstAppearance,
        u.Name AS UniverseName,
        ci.ImageURL,
        f.AddedDate
    FROM Favorites f
    JOIN Characters c       ON f.CharacterID   = c.CharacterID
    LEFT JOIN Universes u   ON c.UniverseID    = u.UniverseID
    LEFT JOIN CharacterImages ci ON c.CharacterID = ci.CharacterID
    WHERE f.UserID = $user_id
    ORDER BY f.AddedDate DESC
");

$favorites = [];
while ($row = mysqli_fetch_assoc($favorites_result)) {
    $row['ImageData'] = !empty($row['ImageURL'])
        ? 'data:image/jpeg;base64,' . base64_encode($row['ImageURL'])
        : null;
    unset($row['ImageURL']);
    $favorites[] = $row;
}

$favorites_count = count($favorites);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/main.css">
    <style>
   
        .profile-section {
            padding: 40px 0 20px;
        }

        .profile-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #ff5252);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }

        .profile-info h2 {
            font-size: 1.6rem;
            color: #333;
            margin-bottom: 6px;
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 8px;
        }

        .profile-meta span {
            font-size: 0.9rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .profile-meta .role-badge {
            background: #ff6b6b;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .profile-stats {
            margin-left: auto;
            text-align: center;
            flex-shrink: 0;
        }

        .profile-stats .stat-num {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ff6b6b;
            line-height: 1;
        }

        .profile-stats .stat-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 4px;
        }

       
        .favorites-section {
            padding: 0 0 60px;
        }

        .favorites-section h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .favorites-section h2 span.count {
            font-size: 1rem;
            background: #ff6b6b;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: normal;
        }

        .empty-favorites {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .empty-favorites p:first-child {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .empty-favorites p:last-child {
            color: #666;
            font-size: 1.1rem;
        }

        .empty-favorites a {
            display: inline-block;
            margin-top: 20px;
            background: #ff6b6b;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .empty-favorites a:hover {
            background: #ff5252;
        }

       
        .fav-card-wrapper {
            position: relative;
        }

        .fav-remove-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
            background: rgba(255,107,107,0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
        }

        .fav-remove-btn:hover {
            background: #ff5252;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
                text-align: center;
            }
            .profile-stats {
                margin-left: 0;
            }
            .profile-meta {
                justify-content: center;
            }
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
            <a href="main.php">Главная</a>
            <a href="heroes.php">Герои</a>
            <a href="villains.php">Злодеи</a>
            <a href="profile.php" class="active">Личный кабинет</a>
        </div>
    </nav>

   
    <section class="profile-section">
        <div class="container">
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php echo htmlspecialchars(mb_strtoupper(mb_substr($username, 0, 1))); ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user_data['Username']); ?></h2>
                    <div class="profile-meta">
                        <span> <?php echo htmlspecialchars($user_data['Email'] ?? '—'); ?></span>
                        <span class="role-badge"><?php echo htmlspecialchars($user_data['Role'] ?? 'user'); ?></span>
                        <span> Зарегистрирован: <?php echo htmlspecialchars(date('d.m.Y', strtotime($user_data['RegistrationDate'] ?? 'now'))); ?></span>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat-num"><?php echo $favorites_count; ?></div>
                    <div class="stat-label">В избранном</div>
                </div>
            </div>
        </div>
    </section>


    <section class="favorites-section">
        <div class="container">
            <h2>
                Избранные персонажи
                <span class="count"><?php echo $favorites_count; ?></span>
            </h2>

            <?php if (empty($favorites)): ?>
                <div class="empty-favorites">
                   
                    <p>У вас пока нет избранных персонажей</p>
                    <a href="main.php">Перейти к каталогу</a>
                </div>
            <?php else: ?>
                <div class="characters-grid" id="favorites-grid">
                    <?php foreach ($favorites as $fav): ?>
                        <div class="fav-card-wrapper" id="fav-wrapper-<?php echo $fav['CharacterID']; ?>">
                           
                            <button
                                class="fav-remove-btn"
                                onclick="removeFavorite(<?php echo $fav['CharacterID']; ?>, this)"
                                title="Убрать из избранного">✕</button>

                            <a href="character.php?id=<?php echo $fav['CharacterID']; ?>" class="character-card-link">
                                <div class="character-card">
                                    <div class="character-image">
                                        <?php if (!empty($fav['ImageData'])): ?>
                                            <img src="<?php echo $fav['ImageData']; ?>"
                                                 alt="<?php echo htmlspecialchars($fav['Name']); ?>"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="character-image-placeholder">
                                                <span><?php echo htmlspecialchars(mb_substr($fav['Name'], 0, 1)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="character-type-badge <?php echo ($fav['CharacterType'] === 'Герой') ? 'hero-badge' : 'villain-badge'; ?>">
                                            <?php echo htmlspecialchars($fav['CharacterType'] ?? ''); ?>
                                        </div>
                                    </div>
                                    <div class="character-info">
                                        <h3><?php echo htmlspecialchars($fav['Name']); ?></h3>
                                        <p class="character-universe">
                                            <span>🌌</span>
                                            <?php echo htmlspecialchars($fav['UniverseName'] ?? 'Неизвестно'); ?>
                                        </p>
                                        <p class="character-description">
                                            <?php echo htmlspecialchars(mb_substr($fav['Biography'] ?? '', 0, 120)) . '...'; ?>
                                        </p>
                                        <?php if (!empty($fav['FirstAppearance'])): ?>
                                            <p class="character-year">
                                                
                                                <?php echo htmlspecialchars($fav['FirstAppearance']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>О проекте</h3>
                <p>Энциклопедия культовых персонажей — это собрание самых известных персонажей из мира кино, игр и комиксов.</p>
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
                <p>info@characterpedia.ru</p>
                <p>+7 (964) 426-79-90</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Энциклопедия культовых персонажей. Все права защищены.</p>
        </div>
    </footer>

    <script>
    function removeFavorite(characterId, btn) {
        btn.disabled = true;
        btn.textContent = '...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../api/favorite.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            var data;
            try { data = JSON.parse(xhr.responseText); } catch(e) { return; }

            if (data.success && data.action === 'removed') {
                var wrapper = document.getElementById('fav-wrapper-' + characterId);
                wrapper.style.transition = 'opacity 0.3s, transform 0.3s';
                wrapper.style.opacity = '0';
                wrapper.style.transform = 'scale(0.9)';
                setTimeout(function () {
                    wrapper.remove();
                    
                    var countEls = document.querySelectorAll('.count, .stat-num');
                    countEls.forEach(function(el) {
                        var n = parseInt(el.textContent, 10);
                        if (!isNaN(n)) el.textContent = Math.max(0, n - 1);
                    });
                 
                    var grid = document.getElementById('favorites-grid');
                    if (grid && grid.children.length === 0) {
                        grid.outerHTML = '<div class="empty-favorites"><p></p><p>У вас пока нет избранных персонажей</p><a href="main.php">Перейти к каталогу</a></div>';
                    }
                }, 300);
            }
        };

        xhr.send('character_id=' + characterId);
    }
    </script>
</body>
</html>