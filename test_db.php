<?php
// Standalone Database Connection Test
header('Content-Type: text/plain');

echo "1. Starting Database Test...\n";

// 1. Check extensions
echo "2. Checking PHP Extensions...\n";
if (!extension_loaded('pdo')) { die("ERROR: PDO extension not loaded\n"); }
if (!extension_loaded('pdo_mysql')) { die("ERROR: pdo_mysql extension not loaded\n"); }
echo "   - PDO and pdo_mysql are loaded.\n";

// 2. Load Environment
echo "3. Loading Environment...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
    echo "   - .env file loaded.\n";
} else {
    echo "   - .env file NOT found at $envFile\n";
}

// 3. Connection Parameters
$host = getenv('DB_HOST') ?: '127.0.0.1';
$name = getenv('DB_NAME') ?: 'khoders_world';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

echo "4. Connection Details:\n";
echo "   - Host: $host\n";
echo "   - Name: $name\n";
echo "   - User: $user\n";
echo "   - Pass: " . ($pass ? '****' : '(empty)') . "\n";

// 4. Attempt Connection
echo "5. Attempting Connection...\n";
try {
    $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "SUCCESS: Connected to database successfully!\n";
    
    // Test Query
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "   - MySQL Version: $version\n";
    
} catch (PDOException $e) {
    echo "ERROR: Connection Failed!\n";
    echo "   - Message: " . $e->getMessage() . "\n";
    echo "   - Code: " . $e->getCode() . "\n";
}
?>
