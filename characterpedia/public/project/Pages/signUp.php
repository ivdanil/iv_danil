<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: main.php');
    exit;
}

$error = '';
$success = '';

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
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Заполните все обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email адрес';
    } elseif (strlen($username) < 3) {
        $error = 'Логин должен содержать минимум 3 символа';
    } elseif (strlen($password) < 4) {
        $error = 'Пароль должен содержать минимум 4 символа';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } else {
        $check_query = "SELECT UserID FROM `Users` WHERE Username = ? OR Email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'Пользователь с таким логином или email уже существует';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO `Users` (Username, PasswordHash, Email, Role, IsEmailConfirmed, RegistrationDate) 
                            VALUES (?, ?, ?, 'user', 0, NOW())";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Регистрация успешна! Теперь вы можете войти.';
                $_POST = [];
            } else {
                $error = 'Ошибка при регистрации: ' . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../../Styles/Pages/signUp.css">
    <style>
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ff6b6b;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .login-link a {
            color: #ff6b6b;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .password-hint {
            font-size: 0.8rem;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-logo">CHARACTERPEDIA</div>
            <p class="login-subtitle">Создайте новый аккаунт</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Логин *</label>
                    <input type="text" id="username" name="username" 
                           placeholder="Введите ваш логин (мин. 3 символа)" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           placeholder="Введите ваш email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль *</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Введите пароль (мин. 4 символа)"
                           required>
                    <div class="password-hint">Пароль должен содержать минимум 4 символа</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтверждение пароля *</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Повторите пароль"
                           required>
                </div>
                
                <button type="submit" class="login-btn">Зарегистрироваться</button>
            </form>
            
            <div class="login-link">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
        </div>
    </div>
</body>
</html>