<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

if (!isset($_SESSION['email']) && !$isAjax) {
    header("Location: ../public/login.php");
    exit;
}
?>
