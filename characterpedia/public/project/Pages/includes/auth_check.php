<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: signUp.php');
    exit;
}

$username = $_SESSION['username'];
?>