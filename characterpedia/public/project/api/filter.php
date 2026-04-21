<?php
header('Content-Type: application/json; charset=utf-8');

$host     = '127.0.1.30';
$port     = 3306;
$user     = 'root';
$password = '';
$database = 'IVANOV_DB';

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    echo json_encode([
        'success' => false,
        'error'   => 'Ошибка подключения к базе данных: ' . mysqli_connect_error()
    ]);
    exit;
}

mysqli_set_charset($conn, 'utf8');

// --- Получаем параметры ---
$universe_id = isset($_GET['universe']) ? (int)$_GET['universe'] : 0;
$type        = isset($_GET['type'])     ? mysqli_real_escape_string($conn, trim($_GET['type'])) : '';
$year_from   = isset($_GET['year_from']) ? (int)$_GET['year_from'] : 0;
$year_to     = isset($_GET['year_to'])   ? (int)$_GET['year_to']   : 0;
$search      = isset($_GET['search'])    ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// --- Строим WHERE ---
$where = [];
if ($universe_id > 0) $where[] = "c.UniverseID = $universe_id";
if (!empty($type))    $where[] = "c.CharacterType = '$type'";
if ($year_from > 0)   $where[] = "CAST(c.FirstAppearance AS UNSIGNED) >= $year_from";
if ($year_to   > 0)   $where[] = "CAST(c.FirstAppearance AS UNSIGNED) <= $year_to";
if (!empty($search))  $where[] = "c.Name LIKE '%$search%'";

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// --- Основной запрос ---
$query = "
    SELECT
        c.CharacterID,
        c.Name,
        c.CharacterType,
        c.Biography,
        c.FirstAppearance,
        c.Status,
        u.Name    AS UniverseName,
        u.UniverseID,
        ci.ImageURL
    FROM Characters c
    LEFT JOIN Universes u  ON c.UniverseID   = u.UniverseID
    LEFT JOIN CharacterImages ci ON c.CharacterID = ci.CharacterID
    $where_clause
    ORDER BY c.Name ASC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([
        'success' => false,
        'error'   => 'Ошибка запроса: ' . mysqli_error($conn)
    ]);
    exit;
}

// --- Получаем название вселенной для заголовка ---
$universe_name = '';
if ($universe_id > 0) {
    $u_res = mysqli_query($conn, "SELECT Name FROM Universes WHERE UniverseID = $universe_id");
    if ($u_res && $u_row = mysqli_fetch_assoc($u_res)) {
        $universe_name = $u_row['Name'];
    }
}

// --- Собираем персонажей ---
$characters = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Конвертируем BLOB-изображение в base64
    if (!empty($row['ImageURL'])) {
        $row['ImageData'] = 'data:image/jpeg;base64,' . base64_encode($row['ImageURL']);
    } else {
        $row['ImageData'] = null;
    }
    unset($row['ImageURL']); // убираем сырой BLOB из JSON
    $characters[] = $row;
}

// --- Счётчик ---
$count_query  = "SELECT COUNT(*) AS total FROM Characters c $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total        = ($count_result && $count_row = mysqli_fetch_assoc($count_result)) ? (int)$count_row['total'] : 0;

mysqli_close($conn);

// --- Возвращаем ответ, включая поле filters для updateTitle() ---
echo json_encode([
    'success'    => true,
    'total'      => $total,
    'characters' => $characters,
    'filters'    => [
        'universe_id'   => $universe_id,
        'universe_name' => $universe_name,
        'type'          => $type,
        'year_from'     => $year_from ?: null,
        'year_to'       => $year_to   ?: null,
        'search'        => $search,
    ]
], JSON_UNESCAPED_UNICODE);
?>