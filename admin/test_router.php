<?php
// Check if router.php exists
$router_path = __DIR__ . '/includes/router.php';
if (file_exists($router_path)) {
    echo "Router file exists at: " . $router_path;
} else {
    echo "Router file does NOT exist at: " . $router_path;
}

// Check if router_fixed.php exists
$router_fixed_path = __DIR__ . '/includes/router_fixed.php';
if (file_exists($router_fixed_path)) {
    echo "<br>Router_fixed file exists at: " . $router_fixed_path;
} else {
    echo "<br>Router_fixed file does NOT exist at: " . $router_fixed_path;
}

// List all files in the includes directory
echo "<br><br>Files in includes directory:<br>";
$files = scandir(__DIR__ . '/includes');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo $file . "<br>";
    }
}
?>
