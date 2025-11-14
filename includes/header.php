<?php
/**
 * Common header include file
 */

// Get the current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Set default values if not already set by template.php
global $title, $meta_description, $meta_keywords;

if (empty($title)) {
    if ($current_page === 'index') {
        $title = 'KHODERS - Campus Coding Club';
    } else {
        // Convert page name to title case
        $title = ucwords(str_replace('-', ' ', $current_page)) . ' - KHODERS';
    }
}

if (empty($meta_description)) {
    $meta_description = 'KHODERS is a campus coding club dedicated to fostering programming skills and technological innovation among students';
}

if (empty($meta_keywords)) {
    $meta_keywords = 'coding, programming, tech, campus, club, KHODERS, Ghana';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo $title; ?></title>
    <meta content="<?php echo $meta_description; ?>" name="description">
    <meta content="<?php echo $meta_keywords; ?>" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
