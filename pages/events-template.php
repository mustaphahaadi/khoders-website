<?php
// Dynamic events template - renders events from database
if (empty($pageData)) $pageData = [];
?>

<section id="courses-events" class="courses-events section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <div class="col-lg-8">
        <?php if (empty($pageData)): ?>
          <div class="alert alert-info">
            <p>No events scheduled at this time. Check back soon!</p>
          </div>
        <?php else: ?>
          <?php foreach ($pageData as $index => $event): ?>
            <article class="event-card" data-aos="fade-up" data-aos-delay="<?php echo 200 + ($index * 100); ?>">
              <div class="row g-0">
                <div class="col-md-4">
                  <div class="event-image">
                    <?php if (!empty($event['image_url'])): ?>
                      <img src="<?php echo htmlspecialchars($event['image_url']); ?>" class="img-fluid" alt="Event">
                    <?php else: ?>
                      <div class="placeholder-image bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-calendar-event text-white" style="font-size: 3rem;"></i>
                      </div>
                    <?php endif; ?>
                    <div class="date-badge">
                      <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                      <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="event-content">
                    <div class="event-meta">
                      <span class="time"><i class="bi bi-clock"></i> <?php echo date('h:i A', strtotime($event['event_date'])); ?></span>
                      <span class="location"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['location'] ?? 'TBA'); ?></span>
                    </div>
                    <h3 class="event-title">
                      <a href="#"><?php echo htmlspecialchars($event['title']); ?></a>
                    </h3>
                    <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                    <div class="event-actions">
                      <?php if (!empty($event['registration_url'])): ?>
                        <a href="<?php echo htmlspecialchars($event['registration_url']); ?>" class="btn btn-primary">RSVP Now</a>
                      <?php else: ?>
                        <a href="<?php echo SiteRouter::getUrl('register'); ?>" class="btn btn-primary">RSVP Now</a>
                      <?php endif; ?>
                      <a href="#" class="btn btn-outline">Learn More</a>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
