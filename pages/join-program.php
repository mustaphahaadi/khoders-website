<?php
require_once 'includes/template.php';
require_once 'config/csrf.php';
$title = 'Join Program - KHODERS Campus Coding Community';
ob_start();
?>
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Join Our Program</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?page=services">Services</a></li> 
            <li class="current">Join Program</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Enroll Section -->
    <section id="enroll" class="enroll section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-8 mx-auto">
            <div class="enrollment-form-wrapper">

              <div class="enrollment-header text-center mb-5" data-aos="fade-up" data-aos-delay="200">
                <h2>Join Our Coding Programs</h2>
                <p>Take your first step into the world of programming with KHODERS. Complete the form below to join one of our free campus coding programs and start building your tech skills today.</p>
              </div>

              <form class="enrollment-form" action="forms/register.php" method="post" data-aos="fade-up" data-aos-delay="300">
                <?php echo CSRFToken::getFieldHTML(); ?>

                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="firstName" class="form-label">First Name *</label>
                      <input type="text" id="firstName" name="firstName" class="form-control" required="" autocomplete="given-name">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="lastName" class="form-label">Last Name *</label>
                      <input type="text" id="lastName" name="lastName" class="form-control" required="" autocomplete="family-name">
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email" class="form-label">Email Address *</label>
                      <input type="email" id="email" name="email" class="form-control" required="" autocomplete="email">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone" class="form-label">Phone Number</label>
                      <input type="tel" id="phone" name="phone" class="form-control" autocomplete="tel">
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="course" class="form-label">Select Program *</label>
                      <select id="course" name="course" class="form-select" required="">
                        <option value="">Choose a program...</option>
                        <option value="web-development">Web Development Pathway</option>
                        <option value="mobile-development">Mobile App Development</option>
                        <option value="ui-ux-design">UI/UX Design Fundamentals</option>
                        <option value="data-science">Data Science Basics</option>
                        <option value="cybersecurity">Cybersecurity Introduction</option>
                        <option value="cloud-computing">Cloud Computing Essentials</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="education" class="form-label">Education Level</label>
                      <select id="education" name="education" class="form-select">
                        <option value="">Select your education level...</option>
                        <option value="high-school">High School</option>
                        <option value="associate">Associate Degree</option>
                        <option value="bachelor">Bachelor's Degree</option>
                        <option value="master">Master's Degree</option>
                        <option value="doctorate">Doctorate</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="experience" class="form-label">Experience Level</label>
                      <select id="experience" name="experience" class="form-select">
                        <option value="">Select your experience...</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="motivation" class="form-label">What motivates you to take this course?</label>
                      <textarea id="motivation" name="motivation" class="form-control" rows="4" placeholder="Share your goals and what you hope to achieve..."></textarea>
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-label">Preferred Learning Schedule</label>
                      <div class="schedule-options">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="schedule" id="weekdays" value="weekdays">
                          <label class="form-check-label" for="weekdays">
                            Weekdays (Monday - Friday)
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="schedule" id="weekends" value="weekends">
                          <label class="form-check-label" for="weekends">
                            Weekends (Saturday - Sunday)
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="schedule" id="flexible" value="flexible" checked="">
                          <label class="form-check-label" for="flexible">
                            Flexible (Self-paced)
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-12">
                    <div class="form-group">
                      <div class="agreement-section">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="terms" name="terms" required="">
                          <label class="form-check-label" for="terms">
                            I agree to the <a href="index.php?page=terms-of-service">Terms of Service</a>, <a href="index.php?page=privacy-policy">Privacy Policy</a>, and <a href="index.php?page=code-of-conduct">Code of Conduct</a> *
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                          <label class="form-check-label" for="newsletter">
                            I would like to receive course updates and educational content via email
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 text-center">
                    <button type="submit" class="btn btn-enroll">
                      <i class="bi bi-check-circle me-2"></i>
                      Join Program
                    </button>
                    <p class="enrollment-note mt-3">
                      <i class="bi bi-shield-check"></i>
                      Your information is secure and will never be shared with third parties
                    </p>
                  </div>
                </div>

              </form>

            </div>
          </div><!-- End Form Column -->

          <div class="col-lg-4 d-none d-lg-block">
            <div class="enrollment-benefits" data-aos="fade-left" data-aos-delay="400">
              <h3>Why Join KHODERS?</h3>
              <div class="benefit-item">
                <div class="benefit-icon">
                  <i class="bi bi-people"></i>
                </div>
                <div class="benefit-content">
                  <h4>Supportive Community</h4>
                  <p>Learn alongside fellow students in a collaborative, encouraging environment</p>
                </div>
              </div><!-- End Benefit Item -->

              <div class="benefit-item">
                <div class="benefit-icon">
                  <i class="bi bi-person-check"></i>
                </div>
                <div class="benefit-content">
                  <h4>Personalized Mentorship</h4>
                  <p>Get one-on-one guidance from experienced mentors who care about your success</p>
                </div>
              </div><!-- End Benefit Item -->

              <div class="benefit-item">
                <div class="benefit-icon">
                  <i class="bi bi-laptop"></i>
                </div>
                <div class="benefit-content">
                  <h4>Hands-On Projects</h4>
                  <p>Build real-world applications and portfolio pieces to showcase your skills</p>
                </div>
              </div><!-- End Benefit Item -->

              <div class="benefit-item">
                <div class="benefit-icon">
                  <i class="bi bi-briefcase"></i>
                </div>
                <div class="benefit-content">
                  <h4>Career Opportunities</h4>
                  <p>Connect with local tech companies and prepare for internships and job opportunities</p>
                </div>
              </div><!-- End Benefit Item -->

              <div class="enrollment-stats mt-4">
                <div class="stat-item">
                  <span class="stat-number">400+</span>
                  <span class="stat-label">Active Members</span>
                </div>
                <div class="stat-item">
                  <span class="stat-number">92%</span>
                  <span class="stat-label">Completion Rate</span>
                </div>
                <div class="stat-item">
                  <span class="stat-number">4.8/5</span>
                  <span class="stat-label">Member Satisfaction</span>
                </div>
              </div><!-- End Stats -->

            </div>
          </div><!-- End Benefits Column -->

        </div>

      </div>

    </section><!-- /Enroll Section -->
<?php
$content = ob_get_clean();
echo render_page($content, $title, ['body_class' => 'enroll-page']);
?>
