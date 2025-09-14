<?php
/**
 * Database Initialization Script
 * Creates tables and sets up the database schema
 */

require_once 'database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Create tables
    $success = $database->createTables();
    
    if ($success) {
        echo "Database tables created successfully!\n";
        
        // Insert sample data if tables are empty
        $sampleEvents = [
            [
                'title' => 'Web Development Workshop',
                'description' => 'Learn the basics of HTML, CSS, and JavaScript',
                'date' => date('Y-m-d', strtotime('+7 days')),
                'time' => '14:00:00',
                'location' => 'Computer Lab A',
                'category' => 'workshop'
            ],
            [
                'title' => 'Python Programming Seminar',
                'description' => 'Introduction to Python programming for beginners',
                'date' => date('Y-m-d', strtotime('+14 days')),
                'time' => '16:00:00',
                'location' => 'Lecture Hall B',
                'category' => 'seminar'
            ]
        ];
        
        // Check if events table is empty
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM events");
        $stmt->execute();
        $eventCount = $stmt->fetch()['count'];
        
        if ($eventCount == 0) {
            foreach ($sampleEvents as $event) {
                $stmt = $db->prepare("
                    INSERT INTO events (title, description, date, time, location, category) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $event['title'],
                    $event['description'],
                    $event['date'],
                    $event['time'],
                    $event['location'],
                    $event['category']
                ]);
            }
            echo "Sample events inserted successfully!\n";
        }
        
    } else {
        echo "Error creating database tables.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
