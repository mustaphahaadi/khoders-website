<?php
session_start();
require_once '../config/auth.php';

Auth::logout();
header('Location: login.php');
exit;
?>
