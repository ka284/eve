<?php
session_start();
require_once __DIR__ . '/config/auth.php';

$auth = new Auth();
$auth->logout();
?>