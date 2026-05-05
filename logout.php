<?php
require_once __DIR__ . '/includes/config.php';
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header('Location: ' . SITE_URL . '/index.php');
exit;