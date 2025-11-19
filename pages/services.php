<?php
require_once 'includes/template.php';
$title = 'Member Services & Support - KHODERS Campus Coding Community';
ob_start();
?>
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Our Services</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Services</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Services Section -->
    <section id="services-2" class="courses-2 section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-3">
            <div class="course-filters" data-aos="fade-right" data-aos-delay="100">
              <h4 class="filter-title">Filter Services</h4>

              <div class="filter-group">
                <h5>Category</h5>
                <div class="filter-options">
                  <label class="filter-checkbox">
                    <input type="checkbox" checked="">
                    <span class="checkmark"></span>
                    All Categories
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Learning
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Mentorship
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Projects
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Career
                  </label>
                </div>
              </div>

              <div class="filter-group">
                <h5>Experience Level</h5>
                <div class="filter-options">
                  <label class="filter-checkbox">
                    <input type="checkbox" checked="">
                    <span class="checkmark"></span>
                    All Levels
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Beginner
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Intermediate
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Advanced
                  </label>
                </div>
              </div>

              <div class="filter-group">
                <h5>Time Commitment</h5>
                <div class="filter-options">
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    One-time Event
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Weekly Sessions
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Ongoing Support
                  </label>
                </div>
              </div>

              <div class="filter-group">
                <h5>Availability</h5>
                <div class="filter-options">
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Available Now
                  </label>
                  <label class="filter-checkbox">
                    <input type="checkbox">
                    <span class="checkmark"></span>
                    Coming Soon
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-9">
            <div class="courses-header" data-aos="fade-left" data-aos-delay="100">
              <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search services...">
              </div>
              <div class="sort-dropdown">
                <select>
                  <option>Sort by: Most Popular</option>
                  <option>Newest First</option>
                  <option>Beginner Friendly</option>
                  <option>Advanced</option>
                </select>
              </div>
            </div>

            <div class="courses-grid" data-aos="fade-up" data-aos-delay="200">
              <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-3.webp" alt="Course" class="img-fluid">
                      <div class="course-badge">Popular</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Learning</span>
                        <span class="level">All Levels</span>
                      </div>
                      <h3>Coding Bootcamps</h3>
                      <p>Intensive weekend coding bootcamps focused on specific technologies like React, Node.js, Python, or mobile app development. Learn practical skills in a collaborative environment.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>Weekend</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>10-15 participants</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-half"></i>
                          <span>4.8 (89 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-m-3.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Led by Senior Mentors</span>
                      </div>
                      <a href="index.php?page=register" class="btn-course">Join Now</a>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-7.webp" alt="Course" class="img-fluid">
                      <div class="course-badge badge-free">Free</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Mentorship</span>
                        <span class="level">Beginner</span>
                      </div>
                      <h3>1-on-1 Mentorship Sessions</h3>
                      <p>Get personalized guidance from experienced developers who can help with specific coding challenges, career advice, or project development. Sessions are tailored to your needs.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>1 hour/week</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>Personal</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star"></i>
                          <span>4.6 (156 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-f-7.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Matched with Mentor</span>
                      </div>
                      <a href="index.php?page=register" class="btn-course">Request Mentor</a>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-12.webp" alt="Course" class="img-fluid">
                      <div class="course-badge badge-new">New</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Projects</span>
                        <span class="level">Intermediate</span>
                      </div>
                      <h3>Real-world Project Teams</h3>
                      <p>Collaborate with other members on real projects for local businesses, non-profits, or campus organizations. Build your portfolio while making a difference in the community.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>10-12 weeks</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>4-6 team members</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <span>5.0 (42 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-m-8.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Project Lead</span>
                      </div>
                      <a href="index.php?page=register" class="btn-course">Join a Project</a>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-5.webp" alt="Course" class="img-fluid">
                      <div class="course-badge">Advanced</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Learning</span>
                        <span class="level">Advanced</span>
                      </div>
                      <h3>Specialized Tech Workshops</h3>
                      <p>Deep-dive workshops into advanced technologies like machine learning, blockchain, cloud architecture, or cybersecurity. Led by industry professionals with hands-on experience.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>4-8 hours</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>Limited seats</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-half"></i>
                          <span>4.7 (73 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-f-12.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Industry Expert</span>
                      </div>
                      <a href="index.php?page=register" class="btn-course">RSVP</a>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-9.webp" alt="Course" class="img-fluid">
                      <div class="course-badge">Ongoing</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Career</span>
                        <span class="level">All Levels</span>
                      </div>
                      <h3>Career Development Support</h3>
                      <p>Resume reviews, interview preparation, portfolio development, and internship/job placement assistance. We connect students with companies looking for tech talent.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>As needed</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>Personalized</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star"></i>
                          <span>4.5 (234 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-m-5.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Career Advisors</span>
                      </div>
                      <a href="index.php?page=register" class="btn-course">Get Support</a>
                    </div>
                  </div>
                </div>

                <div class="col-lg-6 col-md-6">
                  <div class="course-card">
                    <div class="course-image">
                      <img src="assets/img/education/courses-14.webp" alt="Course" class="img-fluid">
                      <div class="course-badge badge-certificate">Event</div>
                    </div>
                    <div class="course-content">
                      <div class="course-meta">
                        <span class="category">Events</span>
                        <span class="level">All Levels</span>
                      </div>
                      <h3>Hackathons & Coding Competitions</h3>
                      <p>Participate in regular hackathons, coding challenges, and team competitions. Build something amazing in a short timeframe, win prizes, and network with peers and professionals.</p>
                      <div class="course-stats">
                        <div class="stat">
                          <i class="bi bi-clock"></i>
                          <span>24-48 hours</span>
                        </div>
                        <div class="stat">
                          <i class="bi bi-people"></i>
                          <span>Teams of 2-4</span>
                        </div>
                        <div class="rating">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-half"></i>
                          <span>4.9 (127 reviews)</span>
                        </div>
                      </div>
                      <div class="instructor-info">
                        <img src="assets/img/person/person-f-9.webp" alt="Instructor" class="instructor-avatar">
                        <span class="instructor-name">Event Coordinator</span>
                      </div>
                      <a href="index.php?page=events" class="btn-course">Upcoming Events</a>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="pagination-wrapper" data-aos="fade-up" data-aos-delay="300">
              <nav aria-label="Services pagination">
                <ul class="pagination justify-content-center">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                      <i class="bi bi-chevron-left"></i>
                    </a>
                  </li>
                  <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="#">2</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="#">
                      <i class="bi bi-chevron-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>

          </div>
        </div>

      </div>

    </section>
<?php
$content = ob_get_clean();
echo render_page($content, $title, ['body_class' => 'services-page']);
?>
