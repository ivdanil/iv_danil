<?php
session_start();
session_destroy();
header('Location: signUp.php');
exit;
?>