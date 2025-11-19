<?php
/**
 * Events Page - Khoders World
 * Displays upcoming events from database
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/router.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';
$categories = ['workshop', 'seminar', 'hackathon', 'meetup', 'conference'];

// Build query
$whereClause = "WHERE date >= CURDATE() AND status = 'upcoming'";
$params = [];
if (!empty($category) && in_array($category, $categories)) {
    $whereClause .= " AND category = ?";
    $params[] = $category;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM events $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalEvents = $countStmt->fetch()['total'];
$totalPages = ceil($totalEvents / $perPage);

// Get events
$query = "SELECT id, title, description, date, time, location, image_url, category, is_featured 
          FROM events 
          $whereClause 
          ORDER BY is_featured DESC, date ASC, time ASC 
          LIMIT ? OFFSET ?";
          
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format dates
foreach ($events as &$event) {
    $event['formatted_date'] = date('F j, Y', strtotime($event['date']));
    $event['formatted_time'] = date('g:i A', strtotime($event['time']));
    $event['day'] = date('d', strtotime($event['date']));
    $event['month'] = date('M', strtotime($event['date']));
    if (empty($event['image_url'])) {
        $event['image_url'] = 'assets/img/education/events-3.webp';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Events - Khoders World</title>
  <meta name="description" content="Join Khoders World coding events, workshops, and hackathons at Kumasi Technical University">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="events-page">
  <?php include __DIR__ . '/../includes/navigation.php'; ?>
  
  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Upcoming Events</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Events</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Events Section -->
    <section id="events" class="events section">
      <div class="container">
        
        <!-- Category Filter -->
        <div class="row mb-4" data-aos="fade-up">
          <div class="col-12">
            <div class="btn-group" role="group" aria-label="Event categories">
              <a href="index.php?page=events" class="btn btn-outline-primary <?php echo empty($category) ? 'active' : ''; ?>">All Events</a>
              <a href="index.php?page=events&category=workshop" class="btn btn-outline-primary <?php echo $category === 'workshop' ? 'active' : ''; ?>">Workshops</a>
              <a href="index.php?page=events&category=seminar" class="btn btn-outline-primary <?php echo $category === 'seminar' ? 'active' : ''; ?>">Seminars</a>
              <a href="index.php?page=events&category=hackathon" class="btn btn-outline-primary <?php echo $category === 'hackathon' ? 'active' : ''; ?>">Hackathons</a>
              <a href="index.php?page=events&category=meetup" class="btn btn-outline-primary <?php echo $category === 'meetup' ? 'active' : ''; ?>">Meetups</a>
            </div>
          </div>
        </div>

        <?php if (empty($events)): ?>
          <!-- Empty State -->
          <div class="row" data-aos="fade-up">
            <div class="col-12 text-center py-5">
              <i class="bi bi-calendar-event" style="font-size: 4rem; color: #136ad5;"></i>
              <h3 class="mt-3">No Upcoming Events</h3>
              <p class="text-muted">Check back soon for exciting workshops and coding events!</p>
              <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
            </div>
          </div>
        <?php else: ?>
          <!-- Events Grid -->
          <div class="row gy-4">
            <?php foreach ($events as $event): ?>
              <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card event-card h-100">
                  <img src="<?php echo htmlspecialchars($event['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>">
                  
                  <div class="card-body">
                    <?php if ($event['is_featured']): ?>
                      <span class="badge bg-warning text-dark mb-2">Featured</span>
                    <?php endif; ?>
                    
                    <?php if ($event['category']): ?>
                      <span class="badge bg-info mb-2"><?php echo ucfirst(htmlspecialchars($event['category'])); ?></span>
                    <?php endif; ?>
                    
                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                    
                    <p class="card-text text-muted">
                      <?php 
                        $description = htmlspecialchars($event['description']);
                        echo strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description; 
                      ?>
                    </p>
                    
                    <div class="event-meta mt-3">
                      <div class="mb-2">
                        <i class="bi bi-calendar3"></i>
                        <span class="ms-2"><?php echo $event['formatted_date']; ?></span>
                      </div>
                      <div class="mb-2">
                        <i class="bi bi-clock"></i>
                        <span class="ms-2"><?php echo $event['formatted_time']; ?></span>
                      </div>
                      <div>
                        <i class="bi bi-geo-alt"></i>
                        <span class="ms-2"><?php echo htmlspecialchars($event['location']); ?></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="card-footer bg-transparent">
                    <a href="index.php?page=enroll&type=event&id=<?php echo $event['id']; ?>" class="btn btn-primary w-100">
                      Register Now
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
            <div class="row mt-5" data-aos="fade-up">
              <div class="col-12">
                <nav aria-label="Events pagination">
                  <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=events<?php echo !empty($category) ? '&category=' . $category : ''; ?>&p=<?php echo $page - 1; ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?page=events<?php echo !empty($category) ? '&category=' . $category : ''; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                      <li class="page-item">
                        <a class="page-link" href="index.php?page=events<?php echo !empty($category) ? '&category=' . $category : ''; ?>&p=<?php echo $page +  1; ?>">Next</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </nav>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
