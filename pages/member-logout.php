<?php
/**
 * Member Logout - Khoders World
 */

require_once __DIR__ . '/../includes/member-auth.php';

// Logout
MemberAuth::logout();

// Redirect to home
header('Location: index.php');
exit;
