<?php
/**
 * KHODERS Database Setup Script
 * 
 * This script will initialize the database and tables required for the KHODERS website
 * Run this script once to set up the database
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials - change these to match your environment
$host = 'localhost';
$username = 'root';  // Default MySQL username
$password = '';      // Default MySQL password (often empty on localhost)
$database = 'khoders_db';

// Connect to MySQL server without selecting a database
try {
    $conn = new mysqli($host, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<h2>Connected to MySQL server successfully!</h2>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $database";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Database '$database' created or already exists.</p>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($database);
    
    // Read and execute SQL from schema.sql
    $schema_file = file_get_contents('schema.sql');
    if ($schema_file === false) {
        throw new Exception("Could not read schema.sql file.");
    }
    
    // Split SQL statements by semicolon
    $statements = explode(';', $schema_file);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if ($conn->query($statement) === TRUE) {
                echo "<p>SQL executed successfully: " . substr($statement, 0, 50) . "...</p>";
            } else {
                echo "<p>Error executing SQL: " . $conn->error . "</p>";
                // Continue with other statements despite errors
            }
        }
    }
    
    // Read and execute SQL from schema_updates.sql
    $schema_updates_file = file_get_contents('schema_updates.sql');
    if ($schema_updates_file === false) {
        throw new Exception("Could not read schema_updates.sql file.");
    }
    
    // Split SQL statements by semicolon
    $statements = explode(';', $schema_updates_file);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if ($conn->query($statement) === TRUE) {
                echo "<p>Update SQL executed successfully: " . substr($statement, 0, 50) . "...</p>";
            } else {
                echo "<p>Error executing update SQL: " . $conn->error . "</p>";
                // Continue with other statements despite errors
            }
        }
    }
    
    echo "<h2>Database setup completed successfully!</h2>";
    
    // Create a new user with limited privileges for the application
    $new_username = 'khoders_user';
    $new_password = 'khoders_password'; // Use a strong password in production
    
    // Drop user if exists and create new one
    $conn->query("DROP USER IF EXISTS '$new_username'@'localhost'");
    
    $sql = "CREATE USER '$new_username'@'localhost' IDENTIFIED BY '$new_password'";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Database user created.</p>";
    } else {
        echo "<p>Error creating database user: " . $conn->error . "</p>";
    }
    
    // Grant privileges
    $sql = "GRANT SELECT, INSERT, UPDATE, DELETE ON $database.* TO '$new_username'@'localhost'";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Privileges granted to database user.</p>";
        $conn->query("FLUSH PRIVILEGES");
    } else {
        echo "<p>Error granting privileges: " . $conn->error . "</p>";
    }
    
    // Close connection
    $conn->close();
    
} catch (Exception $e) {
    die("<h2>Setup failed:</h2><p>" . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHODERS Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .success {
            color: green;
            background-color: #eeffee;
            padding: 10px;
            border-left: 5px solid green;
        }
        .error {
            color: red;
            background-color: #ffeeee;
            padding: 10px;
            border-left: 5px solid red;
        }
        .next-steps {
            margin-top: 30px;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>KHODERS Database Setup</h1>
    
    <div class="next-steps">
        <h3>Next Steps</h3>
        <ol>
            <li>Update the <code>config.php</code> file with your database credentials</li>
            <li>Delete this setup file after successful installation</li>
            <li>Ensure your MySQL server is running when using the website</li>
        </ol>
        <p>For security reasons, after setup is complete, consider changing the database password and updating the credentials in <code>config.php</code>.</p>
        <p><a href="../index.php">Return to Website</a></p>
    </div>
</body>
</html>
