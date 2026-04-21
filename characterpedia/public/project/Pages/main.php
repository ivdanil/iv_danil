<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

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

// --- Вселенные для select ---
$universes = [];
$universe_result = mysqli_query($conn, "SELECT UniverseID, Name FROM Universes ORDER BY Name");
if ($universe_result) {
    while ($row = mysqli_fetch_assoc($universe_result)) {
        $universes[] = $row;
    }
}

// --- Диапазон лет для плейсхолдеров ---
$year_result = mysqli_query($conn, "
    SELECT
        MIN(CAST(FirstAppearance AS UNSIGNED)) AS min_year,
        MAX(CAST(FirstAppearance AS UNSIGNED)) AS max_year
    FROM Characters
    WHERE FirstAppearance IS NOT NULL AND FirstAppearance != ''
");
$year_range = mysqli_fetch_assoc($year_result);
$min_year   = $year_range['min_year'] ?? 1938;
$max_year   = $year_range['max_year'] ?? 2026;

// --- Общий счётчик персонажей ---
$count_result     = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Characters");
$total_characters = mysqli_fetch_assoc($count_result)['total'] ?? 0;

// -----------------------------------------------------------------
// ИСПРАВЛЕНИЕ №1: при первой загрузке получаем ВСЕ персонажи
// (не LIMIT 12), чтобы счётчик и сетка совпадали.
// Если база очень большая — можно вернуть LIMIT и добавить пагинацию,
// но тогда счётчик тоже должен отражать именно это количество.
// -----------------------------------------------------------------
$query = "
    SELECT
        c.CharacterID,
        c.Name,
        c.CharacterType,
        c.Biography,
        c.FirstAppearance,
        c.Status,
        u.Name   AS UniverseName,
        ci.ImageURL
    FROM Characters c
    LEFT JOIN Universes u         ON c.UniverseID   = u.UniverseID
    LEFT JOIN CharacterImages ci  ON c.CharacterID  = ci.CharacterID
    ORDER BY c.Name ASC
";
$result = mysqli_query($conn, $query);

$characters = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Конвертируем BLOB → base64 data-URI
        $row['ImageData'] = !empty($row['ImageURL'])
            ? 'data:image/jpeg;base64,' . base64_encode($row['ImageURL'])
            : null;
        unset($row['ImageURL']);
        $characters[] = $row;
    }
}

mysqli_close($conn);

$current_page = 'main';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/main.css">
   
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
            <a href="main.php" class="active">Главная</a>
            <a href="heroes.php">Герои</a>
            <a href="villains.php">Злодеи</a>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-content">
            <h1>Погрузитесь в мир культовых персонажей</h1>
        </div>
    </section>

    <section class="filter-section">
        <div class="container">
            <h2 class="filter-title">Поиск по персонажам</h2>

            <div class="filter-container">
                <div class="filter-group">
                    <label for="universe-filter">Вселенная</label>
                    <select id="universe-filter" class="filter-select">
                        <option value="">Все вселенные</option>
                        <?php foreach ($universes as $universe): ?>
                            <option value="<?php echo $universe['UniverseID']; ?>">
                                <?php echo htmlspecialchars($universe['Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="type-filter">Тип персонажа</label>
                    <select id="type-filter" class="filter-select">
                        <option value="">Все типы</option>
                        <option value="Герой">Герои</option>
                        <option value="Злодей">Злодеи</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="year-from">Год от</label>
                    <input type="number" id="year-from" class="filter-input"
                           placeholder="<?php echo $min_year; ?>"
                           min="<?php echo $min_year; ?>"
                           max="<?php echo $max_year; ?>"
                           value="">
                </div>

                <div class="filter-group">
                    <label for="year-to">Год до</label>
                    <input type="number" id="year-to" class="filter-input"
                           placeholder="<?php echo $max_year; ?>"
                           min="<?php echo $min_year; ?>"
                           max="<?php echo $max_year; ?>"
                           value="">
                </div>

                <div class="filter-group filter-search">
                    <label for="search-name">Имя персонажа</label>
                    <input type="text" id="search-name" class="filter-input"
                           placeholder="Введите имя персонажа...">
                </div>

                <div class="filter-actions">
                    <button id="apply-filter"  class="filter-btn filter-btn-apply">Применить</button>
                    <button id="reset-filter"  class="filter-btn filter-btn-reset">Сбросить</button>
                </div>
            </div>

            <div id="filter-loader" class="filter-loader" style="display: none;">
                <div class="spinner"></div>
                <span>Загрузка...</span>
            </div>

            <div id="filter-count" class="filter-count">
                Найдено персонажей: <span id="result-count"><?php echo $total_characters; ?></span>
            </div>
        </div>
    </section>

    <section class="characters-section">
        <div class="container">
            <h2 id="results-title">Все персонажи</h2>
            <div id="characters-grid" class="characters-grid">
                <?php foreach ($characters as $character): ?>
                    <a href="character.php?id=<?php echo $character['CharacterID']; ?>" class="character-card-link">
                        <div class="character-card">
                            <div class="character-image">
                                <?php if (!empty($character['ImageData'])): ?>
                                    <img src="<?php echo $character['ImageData']; ?>"
                                         alt="<?php echo htmlspecialchars($character['Name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="character-image-placeholder">
                                        <span><?php echo htmlspecialchars(mb_substr($character['Name'], 0, 1)); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="character-type-badge <?php echo ($character['CharacterType'] === 'Герой') ? 'hero-badge' : 'villain-badge'; ?>">
                                    <?php echo htmlspecialchars($character['CharacterType'] ?? ''); ?>
                                </div>
                            </div>
                            <div class="character-info">
                                <h3><?php echo htmlspecialchars($character['Name']); ?></h3>
                                <p class="character-universe">
                                    <span>🌌</span>
                                    <?php echo htmlspecialchars($character['UniverseName'] ?? 'Неизвестно'); ?>
                                </p>
                                <p class="character-description">
                                    <?php echo htmlspecialchars(mb_substr($character['Biography'] ?? '', 0, 120)) . '...'; ?>
                                </p>
                                <?php if (!empty($character['FirstAppearance'])): ?>
                                    <p class="character-year">
                                        <span>📅</span>
                                        <?php echo htmlspecialchars($character['FirstAppearance']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>О проекте</h3>
                <p>Энциклопедия культовых персонажей — это собрание самых известных и значимых персонажей из мира кино, игр и комиксов.</p>
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
                <div class="social-links">
                    <a href="#">VK</a>
                    <a href="#">TG</a>
                    <a href="#">RT</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 Энциклопедия культовых персонажей. Все права защищены.</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        var universeFilter = document.getElementById('universe-filter');
        var typeFilter     = document.getElementById('type-filter');
        var yearFrom       = document.getElementById('year-from');
        var yearTo         = document.getElementById('year-to');
        var searchName     = document.getElementById('search-name');
        var applyBtn       = document.getElementById('apply-filter');
        var resetBtn       = document.getElementById('reset-filter');
        var charactersGrid = document.getElementById('characters-grid');
        var resultsTitle   = document.getElementById('results-title');
        var resultCount    = document.getElementById('result-count');
        var loader         = document.getElementById('filter-loader');

        // -----------------------------------------------------------------
        // Строим URL запроса к api/filter.php
        // -----------------------------------------------------------------
        function buildFilterUrl() {
            var params = [];
            if (universeFilter.value) params.push('universe=' + encodeURIComponent(universeFilter.value));
            if (typeFilter.value)     params.push('type='     + encodeURIComponent(typeFilter.value));
            if (yearFrom.value)       params.push('year_from='+ encodeURIComponent(yearFrom.value));
            if (yearTo.value)         params.push('year_to='  + encodeURIComponent(yearTo.value));
            if (searchName.value.trim()) params.push('search='+ encodeURIComponent(searchName.value.trim()));
            return '../../api/filter.php' + (params.length ? '?' + params.join('&') : '');
        }

        // -----------------------------------------------------------------
        // Загружаем отфильтрованных персонажей через AJAX
        // -----------------------------------------------------------------
        function loadFilteredCharacters() {
            loader.style.display = 'flex';
            charactersGrid.style.opacity = '0.4';

            var xhr = new XMLHttpRequest();
            xhr.open('GET', buildFilterUrl(), true);

            xhr.onload = function () {
                loader.style.display = 'none';
                charactersGrid.style.opacity = '1';

                if (xhr.status !== 200) {
                    showError('Ошибка сервера: ' + xhr.status);
                    return;
                }

                var data;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch (e) {
                    // Выводим «сырой» ответ, чтобы было видно PHP-ошибку
                    showError('Ошибка разбора ответа. Ответ сервера: ' +
                              xhr.responseText.substring(0, 300));
                    return;
                }

                if (!data.success) {
                    showError(data.error || 'Неизвестная ошибка');
                    return;
                }

                // ИСПРАВЛЕНИЕ №2: data.filters теперь реально приходит из API
                updateTitle(data.filters);
                resultCount.textContent = data.total;
                renderCharacters(data.characters);
            };

            xhr.onerror = function () {
                loader.style.display = 'none';
                charactersGrid.style.opacity = '1';
                showError('Ошибка соединения с сервером');
            };

            xhr.send();
        }

        // -----------------------------------------------------------------
        // Обновляем заголовок секции в зависимости от фильтров
        // -----------------------------------------------------------------
        function updateTitle(filters) {
            if (!filters) { resultsTitle.textContent = 'Все персонажи'; return; }

            var title = 'Все персонажи';
            if (filters.type) {
                title = filters.type === 'Герой' ? 'Культовые герои' : 'Культовые злодеи';
            }
            if (filters.universe_name) {
                title += ' • ' + filters.universe_name;
            }
            if (filters.search) {
                title += ' • "' + filters.search + '"';
            }
            resultsTitle.textContent = title;
        }

        // -----------------------------------------------------------------
        // Рендерим карточки персонажей
        // -----------------------------------------------------------------
        function renderCharacters(characters) {
            if (!characters || characters.length === 0) {
                charactersGrid.innerHTML =
                    '<div class="no-results">' +
                    '<p>Персонажи не найдены</p>' +
                    '<p>Измените параметры фильтрации</p>' +
                    '</div>';
                return;
            }

            var html = '';
            for (var i = 0; i < characters.length; i++) {
                var c = characters[i];

                // Изображение или плейсхолдер
                var imageHtml;
                if (c.ImageData) {
                    imageHtml = '<img src="' + c.ImageData + '" alt="' +
                                escHtml(c.Name) + '" loading="lazy">';
                } else {
                    imageHtml = '<div class="character-image-placeholder"><span>' +
                                escHtml(c.Name.charAt(0).toUpperCase()) + '</span></div>';
                }

                var typeClass = (c.CharacterType === 'Герой') ? 'hero-badge' : 'villain-badge';
                var bio       = (c.Biography || '').substring(0, 120);
                var yearHtml  = c.FirstAppearance
                    ? '<p class="character-year"><span>📅</span> ' + escHtml(c.FirstAppearance) + '</p>'
                    : '';

                html += '<a href="character.php?id=' + c.CharacterID + '" class="character-card-link">';
                html += '<div class="character-card">';
                html +=   '<div class="character-image">';
                html +=     imageHtml;
                html +=     '<div class="character-type-badge ' + typeClass + '">' +
                                escHtml(c.CharacterType || '') +
                            '</div>';
                html +=   '</div>';
                html +=   '<div class="character-info">';
                html +=     '<h3>'  + escHtml(c.Name) + '</h3>';
                html +=     '<p class="character-universe"><span>🌌</span> ' +
                                escHtml(c.UniverseName || 'Неизвестно') + '</p>';
                html +=     '<p class="character-description">' + escHtml(bio) + '...</p>';
                html +=     yearHtml;
                html +=   '</div>';
                html += '</div></a>';
            }

            charactersGrid.innerHTML = html;
        }

        // -----------------------------------------------------------------
        // Сброс фильтров → сбрасываем поля и делаем запрос без параметров
        // -----------------------------------------------------------------
        function resetFilters() {
            universeFilter.value = '';
            typeFilter.value     = '';
            yearFrom.value       = '';
            yearTo.value         = '';
            searchName.value     = '';
            resultsTitle.textContent = 'Все персонажи';
            loadFilteredCharacters(); // запрос без параметров = все персонажи
        }

        // -----------------------------------------------------------------
        // Вспомогательная: экранирование HTML для JS-генерируемого контента
        // -----------------------------------------------------------------
        function escHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function showError(message) {
            charactersGrid.innerHTML =
                '<div class="error-message">' +
                '<p>Ошибка</p>' +
                '<p>' + escHtml(message) + '</p>' +
                '</div>';
        }

        // -----------------------------------------------------------------
        // Слушатели событий
        // -----------------------------------------------------------------
        applyBtn.addEventListener('click', loadFilteredCharacters);
        resetBtn.addEventListener('click', resetFilters);

        // Enter в поле поиска
        searchName.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') loadFilteredCharacters();
        });

        // Автокоррекция диапазона лет
        yearFrom.addEventListener('change', function () {
            var from = parseInt(this.value, 10) || 0;
            var to   = parseInt(yearTo.value, 10) || 0;
            if (to && from > to) yearTo.value = from;
        });

        yearTo.addEventListener('change', function () {
            var from = parseInt(yearFrom.value, 10) || 0;
            var to   = parseInt(this.value, 10) || 0;
            if (from && to < from) yearFrom.value = to;
        });

    });
    </script>
</body>
</html>