<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$host     = '127.0.1.30';
$port     = 3306;
$user     = 'root';
$password = '';
$database = 'IVANOV_DB';

$conn = mysqli_connect($host, $user, $password, $database, $port);
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Ошибка подключения']);
    exit;
}
mysqli_set_charset($conn, 'utf8');

$user_id      = (int)$_SESSION['user_id'];
$character_id = isset($_POST['character_id']) ? (int)$_POST['character_id'] : 0;

if ($character_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Неверный ID персонажа']);
    exit;
}

$check = mysqli_query($conn, "
    SELECT FavoriteID FROM Favorites
    WHERE UserID = $user_id AND CharacterID = $character_id
");

if (mysqli_num_rows($check) > 0) {
    
    mysqli_query($conn, "
        DELETE FROM Favorites WHERE UserID = $user_id AND CharacterID = $character_id
    ");
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    
    mysqli_query($conn, "
        INSERT INTO Favorites (UserID, CharacterID) VALUES ($user_id, $character_id)
    ");
    echo json_encode(['success' => true, 'action' => 'added']);
}

mysqli_close($conn);
?>