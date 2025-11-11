<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/env.php';

header('Content-Type: text/html; charset=utf-8');

$database = new Database();
$result = $database->testConnection();
$db = $database->getConnection();

$tables = [];
$error = '';

if ($db) {
    try {
        $stmt = $db->query('SHOW TABLES');
        $tables = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
    } catch (PDOException $e) {
        $error = 'Failed to list tables: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connectivity Test</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f7fb; margin: 0; padding: 2rem; color: #1f2937; }
        .card { max-width: 720px; margin: 0 auto; background: #ffffff; border-radius: 0.75rem; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); padding: 2rem; }
        h1 { margin-top: 0; font-size: 1.75rem; }
        .status { display: inline-flex; align-items: center; padding: 0.4rem 0.9rem; border-radius: 9999px; font-weight: 600; margin-bottom: 1.5rem; }
        .status.success { background-color: #dcfce7; color: #166534; }
        .status.error { background-color: #fee2e2; color: #991b1b; }
        .details { margin-top: 1.5rem; }
        .details pre { background: #0f172a; color: #e2e8f0; border-radius: 0.5rem; padding: 1rem; overflow-x: auto; }
        .tables { margin: 1.5rem 0; }
        .tables ul { list-style: none; padding: 0; }
        .tables li { background: #f3f4f6; margin-bottom: 0.5rem; padding: 0.6rem 0.8rem; border-radius: 0.5rem; }
        .footer { margin-top: 2rem; font-size: 0.875rem; color: #6b7280; }
        code { background: #f3f4f6; padding: 0.15rem 0.35rem; border-radius: 0.35rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Database Connectivity Test</h1>
        <?php if ($result['success']): ?>
            <div class="status success">✓ Connection Successful</div>
            <p><?php echo htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <div class="status error">⚠️ Connection Failed</div>
            <p><?php echo htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if ($db && empty($error)): ?>
            <div class="tables">
                <h2>Available Tables</h2>
                <?php if (empty($tables)): ?>
                    <p>No tables found in the database.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($tables as $table): ?>
                            <li><?php echo htmlspecialchars($table, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php elseif ($error): ?>
            <div class="details">
                <h2>Table Listing Error</h2>
                <pre><?php echo $error; ?></pre>
            </div>
        <?php endif; ?>

        <div class="details">
            <h2>Environment</h2>
            <pre><?php
                echo "DB_HOST: " . htmlspecialchars(getenv('DB_HOST') ?: 'localhost', ENT_QUOTES, 'UTF-8') . "\n";
                echo "DB_NAME: " . htmlspecialchars(getenv('DB_NAME') ?: 'khoders_db', ENT_QUOTES, 'UTF-8') . "\n";
                echo "DB_USER: " . htmlspecialchars(getenv('DB_USER') ?: 'khoders_user', ENT_QUOTES, 'UTF-8') . "\n";
            ?></pre>
        </div>

        <div class="footer">
            Update credentials in <code>.env</code> or <code>config/database.php</code> if the connection fails. Remember to remove this file in production environments.
        </div>
    </div>
</body>
</html>
