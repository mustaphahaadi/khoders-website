<?php
/**
 * Check and Insert Sample Data
 * Run this script once to populate courses and programs tables
 */

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div style='color: red; padding: 20px; border: 2px solid red; margin: 20px;'>";
    echo "<h2>Database Connection Failed!</h2>";
    echo "<p><strong>Please check:</strong></p>";
    echo "<ol>";
    echo "<li>XAMPP MySQL is running</li>";
    echo "<li>Database 'khoders_db' exists</li>";
    echo "<li>.env file has correct credentials (default: root with no password)</li>";
    echo "</ol>";
    echo "<p><strong>Current .env settings:</strong></p>";
    echo "<pre>";
    echo "DB_HOST: " . (getenv('DB_HOST') ?: 'localhost') . "\n";
    echo "DB_NAME: " . (getenv('DB_NAME') ?: 'khoders_db') . "\n";
    echo "DB_USER: " . (getenv('DB_USER') ?: 'root') . "\n";
    echo "DB_PASS: " . (getenv('DB_PASS') !== false ? '[SET]' : '[EMPTY]') . "\n";
    echo "</pre>";
    echo "<p><a href='setup.php'>Run Database Setup</a></p>";
    echo "</div>";
    die();
}

echo "<h2>Checking Database Tables...</h2>";

// Check courses table
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM courses WHERE status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $coursesCount = $result['count'];
    
    echo "<p>Active Courses: <strong>$coursesCount</strong></p>";
    
    if ($coursesCount == 0) {
        echo "<p>Inserting sample courses...</p>";
        
        $courses = [
            [
                'title' => 'Web Development Fundamentals',
                'subtitle' => 'Master HTML, CSS, and JavaScript from scratch',
                'category' => 'Web Development',
                'level' => 'Beginner',
                'duration' => '12 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Learn the core technologies that power the modern web. Build responsive websites and interactive applications.',
                'members_count' => 45,
                'rating' => 4.8,
                'status' => 'active'
            ],
            [
                'title' => 'Python Programming',
                'subtitle' => 'From basics to advanced Python development',
                'category' => 'Programming',
                'level' => 'Intermediate',
                'duration' => '10 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Dive deep into Python programming. Learn data structures, algorithms, and real-world applications.',
                'members_count' => 38,
                'rating' => 4.9,
                'status' => 'active'
            ],
            [
                'title' => 'Mobile App Development',
                'subtitle' => 'Build iOS and Android apps with React Native',
                'category' => 'Mobile Development',
                'level' => 'Intermediate',
                'duration' => '14 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Create cross-platform mobile applications using modern frameworks and best practices.',
                'members_count' => 29,
                'rating' => 4.7,
                'status' => 'active'
            ]
        ];
        
        $insertStmt = $db->prepare("INSERT INTO courses (title, subtitle, category, level, duration, hero_image, description, members_count, rating, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        foreach ($courses as $course) {
            $insertStmt->execute([
                $course['title'],
                $course['subtitle'],
                $course['category'],
                $course['level'],
                $course['duration'],
                $course['hero_image'],
                $course['description'],
                $course['members_count'],
                $course['rating'],
                $course['status']
            ]);
        }
        
        echo "<p style='color: green;'>✓ Sample courses inserted successfully!</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error with courses table: " . $e->getMessage() . "</p>";
}

// Check programs table
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM programs WHERE status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $programsCount = $result['count'];
    
    echo "<p>Active Programs: <strong>$programsCount</strong></p>";
    
    if ($programsCount == 0) {
        echo "<p>Inserting sample programs...</p>";
        
        $programs = [
            [
                'title' => 'Full-Stack Developer Track',
                'subtitle' => 'Become a complete web developer',
                'category' => 'Web Development',
                'level' => 'Beginner to Advanced',
                'duration' => '24 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Comprehensive program covering frontend, backend, databases, and deployment. Build real-world projects.',
                'members_count' => 67,
                'rating' => 4.9,
                'status' => 'active'
            ],
            [
                'title' => 'Data Science Bootcamp',
                'subtitle' => 'Master data analysis and machine learning',
                'category' => 'Data Science',
                'level' => 'Intermediate',
                'duration' => '16 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Learn Python, statistics, data visualization, and machine learning. Work on industry projects.',
                'members_count' => 42,
                'rating' => 4.8,
                'status' => 'active'
            ],
            [
                'title' => 'UI/UX Design Program',
                'subtitle' => 'Design beautiful and functional interfaces',
                'category' => 'Design',
                'level' => 'Beginner',
                'duration' => '12 weeks',
                'hero_image' => 'assets/img/education/courses-1.webp',
                'description' => 'Master design principles, user research, prototyping, and modern design tools.',
                'members_count' => 35,
                'rating' => 4.7,
                'status' => 'active'
            ]
        ];
        
        $insertStmt = $db->prepare("INSERT INTO programs (title, subtitle, category, level, duration, hero_image, description, members_count, rating, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        foreach ($programs as $program) {
            $insertStmt->execute([
                $program['title'],
                $program['subtitle'],
                $program['category'],
                $program['level'],
                $program['duration'],
                $program['hero_image'],
                $program['description'],
                $program['members_count'],
                $program['rating'],
                $program['status']
            ]);
        }
        
        echo "<p style='color: green;'>✓ Sample programs inserted successfully!</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error with programs table: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p>✓ Database check complete!</p>";
echo "<p><a href='../index.php?page=courses'>View Courses Page</a> | <a href='../index.php?page=programs'>View Programs Page</a></p>";
echo "<p><strong>Note:</strong> You can delete this file after running it once.</p>";
echo "<p style='margin-top: 20px; padding: 10px; background: #f0f0f0;'><strong>Tip:</strong> If tables don't exist, run <a href='setup.php'>database setup</a> first.</p>";
?>
