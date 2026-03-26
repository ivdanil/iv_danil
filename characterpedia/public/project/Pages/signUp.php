<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: main.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (!empty($username) && !empty($password)) {
        $_SESSION['username'] = $username;
        header('Location: main.php');
        exit;
    } else {
        $error = 'Введите логин и пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - CHARACTERPEDIA</title>
    <link rel="stylesheet" href="../Styles/Global/fonts.css">
    <link rel="stylesheet" href="../Styles/Pages/signUp.css">
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
        </div>
    </div>
</body>
</html>