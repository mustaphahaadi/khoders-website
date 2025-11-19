<?php
/**
 * KHODERS WORLD - Dynamic Homepage
 * Displays featured content from database
 */

// Database connection
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    // If in development, show the error
    if (getenv('APP_ENV') !== 'production') {
        die("Database connection failed. Error: " . $database->getError());
    } else {
        // In production, show a generic message
        include __DIR__ . '/../pages/500.html';
        exit;
    }
}

// Fetch featured skills (for Technology Areas section)
$skillsQuery = "SELECT * FROM skills WHERE status = 'active' AND is_featured = 1 ORDER BY order_index ASC LIMIT 12";
$skillsStmt = $db->prepare($skillsQuery);
$skillsStmt->execute();
$featuredSkills = $skillsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured courses
$coursesQuery = "SELECT * FROM courses WHERE status = 'active' AND is_featured = 1 ORDER BY created_at DESC LIMIT 3";
$coursesStmt = $db->prepare($coursesQuery);
$coursesStmt->execute();
$featuredCourses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured programs
$programsQuery = "SELECT * FROM programs WHERE status = 'active' AND is_featured = 1 ORDER BY created_at DESC LIMIT 3";
$programsStmt = $db->prepare($programsQuery);
$programsStmt->execute();
$featuredPrograms = $programsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured team members
$teamQuery = "SELECT * FROM team_members WHERE status = 'active' AND is_featured = 1 ORDER BY order_index ASC LIMIT 4";
$teamStmt = $db->prepare($teamQuery);
$teamStmt->execute();
$featuredTeam = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured projects
$projectsQuery = "SELECT * FROM projects WHERE status = 'active' AND is_featured = 1 ORDER BY created_at DESC LIMIT 3";
$projectsStmt = $db->prepare($projectsQuery);
$projectsStmt->execute();
$featuredProjects = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming featured events
$eventsQuery = "SELECT * FROM events WHERE status = 'upcoming' AND is_featured = 1 ORDER BY date ASC LIMIT 3";
$eventsStmt = $db->prepare($eventsQuery);
$eventsStmt->execute();
$featuredEvents = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

// Output the homepage content
ob_start();
?>
<main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">
      <img src="assets/img/hero-bg.jpg" alt="" data-aos="fade-in">

      <div class="container">
        <div class="row justify-content-center text-center" data-aos="fade-up" data-aos-delay="100">
          <div class="col-xl-6 col-lg-8">
            <h2>Welcome to KHODERS WORLD<span>.</span></h2>
            <p>Campus Coding Club - Learn, Build, and Innovate Together</p>
          </div>
        </div>
      </div>
    </section>

    <?php if (count($featuredSkills) > 0): ?>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row">
                <div class="col-lg-6 d-flex align-items-center">
                    <img src="assets/img/skills.png" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 content">
                    <h3>Technology Areas</h3>
                    <p class="fst-italic">
                        We explore a wide range of technologies to prepare you for the industry.
                    </p>
                    <div class="row">
                        <?php foreach ($featuredSkills as $skill): ?>
                        <div class="col-lg-6">
                            <div class="skills-content">
                                <div class="progress">
                                    <span class="skill">
                                        <i class="<?php echo htmlspecialchars($skill['icon']); ?> me-2"></i>
                                        <?php echo htmlspecialchars($skill['name']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <?php if (count($featuredCourses) > 0): ?>
    <section id="courses" class="courses section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Featured Courses</h2>
            <p>Start your learning journey with our top-rated courses</p>
        </div>
        <div class="container">
            <div class="row gy-4">
                <?php foreach ($featuredCourses as $course): ?>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                    <div class="course-item">
                        <img src="<?php echo !empty($course['image_url']) ? htmlspecialchars($course['image_url']) : 'assets/img/course-1.jpg'; ?>" class="img-fluid" alt="...">
                        <div class="course-content">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="category"><?php echo htmlspecialchars($course['category']); ?></p>
                                <p class="price"><?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?></p>
                            </div>
                            <h3><a href="index.php?page=course-details&id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></h3>
                            <p class="description"><?php echo htmlspecialchars($course['subtitle']); ?></p>
                            <div class="trainer d-flex justify-content-between align-items-center">
                                <div class="trainer-profile d-flex align-items-center">
                                    <img src="assets/img/trainers/trainer-1.jpg" class="img-fluid" alt="">
                                    <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                                </div>
                                <div class="trainer-rank d-flex align-items-center">
                                    <i class="bi bi-person user-icon"></i>&nbsp;<?php echo $course['enrollment_count']; ?>
                                    &nbsp;&nbsp;
                                    <i class="bi bi-heart heart-icon"></i>&nbsp;<?php echo $course['rating']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <?php if (count($featuredEvents) > 0): ?>
    <section id="events" class="events section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Upcoming Events</h2>
            <p>Join us for our upcoming workshops and hackathons</p>
        </div>
        <div class="container">
            <div class="row gy-4">
                <?php foreach ($featuredEvents as $event): ?>
                <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                    <div class="card">
                        <div class="card-img">
                            <img src="<?php echo !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'assets/img/events-item-1.jpg'; ?>" alt="...">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><a href=""><?php echo htmlspecialchars($event['title']); ?></a></h5>
                            <p class="fst-italic text-center"><?php echo date('l, F jS, Y', strtotime($event['date'])); ?> at <?php echo date('g:i A', strtotime($event['time'])); ?></p>
                            <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>
<?php
$content = ob_get_clean();

// Render the page using the template
require_once __DIR__ . '/../includes/template.php';
echo render_page($content, 'Home - KHODERS WORLD', [
    'description' => 'Welcome to Khoders World - The Ultimate Campus Coding Club',
    'keywords' => 'coding, programming, club, campus, students, learn, build'
]);
?>
