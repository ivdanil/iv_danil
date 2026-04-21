<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: main.php');
    exit;
}

$error = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (!empty($username) && !empty($password)) {
        // Поиск пользователя
        $query = "SELECT * FROM `Users` WHERE `Username` = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user_data = mysqli_fetch_assoc($result)) {
            // Проверка пароля
            if (password_verify($password, $user_data['PasswordHash']) || $password == $user_data['PasswordHash']) {
                $_SESSION['username'] = $user_data['Username'];
                $_SESSION['user_id'] = $user_data['UserID'];
                $_SESSION['role'] = $user_data['Role'];
                $_SESSION['email'] = $user_data['Email'];
                
                header('Location: main.php');
                exit;
            } else {
                $error = 'Неверный логин или пароль';
            }
        } else {
            $error = 'Неверный логин или пароль';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = 'Введите логин и пароль';
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/signUp.css">
    <style>
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: #ff6b6b;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-logo">CHARACTERPEDIA</div>
            <p class="login-subtitle">Войдите в свой аккаунт</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Логин:</label>
                    <input type="text" id="username" name="username" 
                           placeholder="Введите ваш логин" required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Введите ваш пароль" required>
                </div>
                <button type="submit" class="login-btn">Войти</button>
            </form>
            
            <div class="register-link">
                Нет аккаунта? <a href="signUp.php">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</body>
</html>