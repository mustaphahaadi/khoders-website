<?php
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$events = [];
if ($db) {
    try {
        $query = "SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 10";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log('[ERROR] Events fetch failed: ' . $e->getMessage());
    }
}
?>

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Events</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li class="current">Events</li>
      </ol>
    </nav>
  </div>
</div>

    <!-- Club Events Section -->
    <section id="courses-events" class="courses-events section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">

          <div class="col-lg-8">

            <?php if (empty($events)): ?>
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No upcoming events at the moment. Check back soon!
              </div>
            <?php else: ?>
              <?php foreach ($events as $index => $event): 
                $eventDate = new DateTime($event['event_date']);
                $day = $eventDate->format('d');
                $month = $eventDate->format('M');
                $time = $eventDate->format('g:i A');
                $imgPath = 'assets/img/education/events-3.webp';
                if (!empty($event['image_url'])) {
                    $imgPath = $event['image_url'];
                    if (strpos($imgPath, '/public/') === 0) {
                        $imgPath = substr($imgPath, 1);
                    }
                    $imgPath = htmlspecialchars($imgPath);
                }
              ?>
              <!-- Event Item -->
              <article class="event-card" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 100); ?>">
                <div class="row g-0">
                  <div class="col-md-4">
                    <div class="event-image">
                      <img src="<?php echo $imgPath; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($event['title'] ?? ''); ?>">
                      <div class="date-badge">
                        <span class="day"><?php echo $day; ?></span>
                        <span class="month"><?php echo $month; ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="event-content">
                      <div class="event-meta">
                        <span class="time"><i class="bi bi-clock"></i> <?php echo $time; ?></span>
                        <?php if (!empty($event['location'])): ?>
                          <span class="location"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['location'] ?? ''); ?></span>
                        <?php endif; ?>
                      </div>
                      <h3 class="event-title">
                        <a href="#"><?php echo htmlspecialchars($event['title'] ?? ''); ?></a>
                      </h3>
                      <p class="event-description"><?php echo htmlspecialchars($event['description'] ?? ''); ?></p>
                      <div class="event-footer">
                        <div class="instructor">
                          <img src="assets/img/person/person-f-8.webp" alt="Instructor" class="instructor-avatar">
                          <span>KHODERS Team</span>
                        </div>
                        <div class="event-price">
                          <span class="price">Free</span>
                        </div>
                      </div>
                      <div class="event-actions">
                        <?php if (!empty($event['registration_url'])): ?>
                          <a href="<?php echo htmlspecialchars($event['registration_url'] ?? ''); ?>" class="btn btn-primary" target="_blank">RSVP Now</a>
                        <?php else: ?>
                          <a href="index.php?page=enroll&type=event&id=<?php echo (int)($event['id'] ?? 0); ?>" class="btn btn-primary">RSVP Now</a>
                        <?php endif; ?>
                        <a href="#" class="btn btn-outline">Learn More</a>
                      </div>
                    </div>
                  </div>
                </div>
              </article><!-- End Event Item -->
              <?php endforeach; ?>
            <?php endif; ?>

          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">

            <!-- Search Widget -->
            <div class="sidebar-widget search-widget" data-aos="fade-up" data-aos-delay="200">
              <h4 class="widget-title">Search Events</h4>
              <form class="search-form">
                <input type="text" placeholder="Search events..." class="form-control">
                <button type="submit" class="search-btn">
                  <i class="bi bi-search"></i>
                </button>
              </form>
            </div><!-- End Search Widget -->

            <!-- Filter Widget -->
            <div class="sidebar-widget filter-widget" data-aos="fade-up" data-aos-delay="300">
              <h4 class="widget-title">Filter Events</h4>
              <div class="filter-content">
                <div class="filter-group">
                  <label class="filter-label">Event Type</label>
                  <select class="form-select">
                    <option value="">All Types</option>
                    <option value="workshop">Workshop</option>
                    <option value="hackathon">Hackathon</option>
                    <option value="tech-talk">Tech Talk</option>
                    <option value="networking">Networking</option>
                    <option value="competition">Coding Competition</option>
                  </select>
                </div>
                <div class="filter-group">
                  <label class="filter-label">Date Range</label>
                  <select class="form-select">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">Next 3 Months</option>
                  </select>
                </div>
                <div class="filter-group">
                  <label class="filter-label">Price</label>
                  <select class="form-select">
                    <option value="">All Prices</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                  </select>
                </div>
                <button class="btn btn-primary filter-apply-btn">Apply Filters</button>
              </div>
            </div><!-- End Filter Widget -->

            <!-- Upcoming Events Widget -->
            <div class="sidebar-widget upcoming-widget" data-aos="fade-up" data-aos-delay="400">
              <h4 class="widget-title">Upcoming Events</h4>
              <div class="upcoming-list">

                <?php 
                $upcomingEvents = array_slice($events, 0, 3);
                foreach ($upcomingEvents as $upEvent): 
                  $upDate = new DateTime($upEvent['event_date']);
                ?>
                <div class="upcoming-item">
                  <div class="upcoming-date">
                    <span class="day"><?php echo $upDate->format('d'); ?></span>
                    <span class="month"><?php echo $upDate->format('M'); ?></span>
                  </div>
                  <div class="upcoming-content">
                    <h5 class="upcoming-title">
                      <a href="#"><?php echo htmlspecialchars($upEvent['title'] ?? ''); ?></a>
                    </h5>
                    <div class="upcoming-meta">
                      <span class="time"><i class="bi bi-clock"></i> <?php echo $upDate->format('g:i A'); ?></span>
                      <span class="price">Free</span>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>

              </div>
            </div><!-- End Upcoming Events Widget -->

            <!-- Newsletter Widget -->
            <div class="sidebar-widget newsletter-widget" data-aos="fade-up" data-aos-delay="500">
              <h4 class="widget-title">Stay Updated</h4>
              <p>Subscribe to our newsletter for upcoming workshops, hackathons, tech talks, and opportunities for student developers.</p>
              <form action="forms/newsletter.php" method="post" class="php-email-form newsletter-form">
                <input type="email" name="email" placeholder="Your email address" required="">
                <button type="submit">Subscribe</button>
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your subscription request has been sent. Thank you!</div>
              </form>
            </div><!-- End Newsletter Widget -->

          </div>

        </div>

      </div>

</section>
