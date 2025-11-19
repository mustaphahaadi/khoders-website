<?php
require_once 'includes/template.php';
$title = 'Mentors & Advisors - KHODERS Campus Coding Community';
ob_start();
?>
    <!-- Hero Section -->
    <section class="section mentors-hero light-background">
      <div class="container" data-aos="fade-up">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6">
            <h1>Guidance from Mentors Who’ve Been There</h1>
            <p>Our mentor network pairs you with experienced developers, designers, and product leaders who dedicate time each week to accelerate your progress and open doors in the tech industry.</p>
            <div class="hero-highlights">
              <div class="highlight">
                <i class="bi bi-people"></i>
                <span>60+ active mentors</span>
              </div>
              <div class="highlight">
                <i class="bi bi-calendar-week"></i>
                <span>Weekly office hours</span>
              </div>
              <div class="highlight">
                <i class="bi bi-chat-dots"></i>
                <span>Personalized feedback</span>
              </div>
            </div>
            <div class="hero-actions">
              <a href="#mentor-roster" class="btn btn-primary">Browse Mentors</a>
              <a href="index.php?page=register" class="btn btn-outline">Request a Mentor</a>
            </div>
          </div>
          <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="150">
            <img src="assets/img/education/mentorship-session.webp" alt="Mentor guiding a student developer" class="img-fluid rounded-4 shadow">
          </div>
        </div>
      </div>
    </section>

    <!-- Mentor Spotlight -->
    <section id="mentor-roster" class="section mentors-list">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Mentor Spotlight</h2>
          <p>Each learning track features a dedicated group of mentors with deep expertise in their domain.</p>
        </div>

        <div class="row gy-4">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-m-5.webp" alt="Samuel Mensah, senior full-stack engineer" class="img-fluid">
                <span class="badge">Full-Stack</span>
              </div>
              <div class="mentor-info">
                <h3>Samuel Mensah</h3>
                <p class="role">Senior Full-Stack Engineer @ Andela</p>
                <ul>
                  <li><i class="bi bi-check2"></i> Leads weekly code reviews</li>
                  <li><i class="bi bi-check2"></i> Helps launch client capstones</li>
                  <li><i class="bi bi-check2"></i> Career coaching for internships</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Samuel Mensah on LinkedIn"><i class="bi bi-linkedin"></i></a>
                  <a href="#" aria-label="Samuel Mensah on GitHub"><i class="bi bi-github"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-f-6.webp" alt="Afua Boateng, data scientist" class="img-fluid">
                <span class="badge badge-alt">Data & AI</span>
              </div>
              <div class="mentor-info">
                <h3>Afua Boateng</h3>
                <p class="role">Lead Data Scientist @ Meltwater</p>
                <ul>
                  <li><i class="bi bi-check2"></i> Hosts ML experiment labs</li>
                  <li><i class="bi bi-check2"></i> Guides Kaggle-style challenges</li>
                  <li><i class="bi bi-check2"></i> Reviews research poster drafts</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Afua Boateng on LinkedIn"><i class="bi bi-linkedin"></i></a>
                  <a href="#" aria-label="Afua Boateng portfolio"><i class="bi bi-globe"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-f-11.webp" alt="Gifty Owusu, product designer" class="img-fluid">
                <span class="badge">Product & UX</span>
              </div>
              <div class="mentor-info">
                <h3>Gifty Owusu</h3>
                <p class="role">Senior Product Designer @ Flutterwave</p>
                <ul>
                  <li><i class="bi bi-check2"></i> Runs design critique circles</li>
                  <li><i class="bi bi-check2"></i> Coaches portfolio storytelling</li>
                  <li><i class="bi bi-check2"></i> Partners on hackathon teams</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Gifty Owusu on Behance"><i class="bi bi-behance"></i></a>
                  <a href="#" aria-label="Gifty Owusu on Dribbble"><i class="bi bi-dribbble"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-m-10.webp" alt="Kwesi Danquah, cloud architect" class="img-fluid">
                <span class="badge badge-alt">DevOps</span>
              </div>
              <div class="mentor-info">
                <h3>Kwesi Danquah</h3>
                <p class="role">Cloud Architect @ Microsoft</p>
                <ul>
                  <li><i class="bi bi-check2"></i> Azure/AWS infrastructure clinics</li>
                  <li><i class="bi bi-check2"></i> CI/CD pipeline walkthroughs</li>
                  <li><i class="bi bi-check2"></i> Certification study groups</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Kwesi Danquah on LinkedIn"><i class="bi bi-linkedin"></i></a>
                  <a href="#" aria-label="Kwesi Danquah technical blog"><i class="bi bi-journal-code"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-m-9.webp" alt="Kojo Baffoe, cybersecurity specialist" class="img-fluid">
                <span class="badge">Security</span>
              </div>
              <div class="mentor-info">
                <h3>Kojo Baffoe</h3>
                <p class="role">Security Specialist @ PwC</p>
                <ul>
                  <li><i class="bi bi-check2"></i> Leads CTF competitions</li>
                  <li><i class="bi bi-check2"></i> Teaches secure coding lab</li>
                  <li><i class="bi bi-check2"></i> Supports SOC career paths</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Kojo Baffoe on LinkedIn"><i class="bi bi-linkedin"></i></a>
                  <a href="#" aria-label="Kojo Baffoe on Twitter"><i class="bi bi-twitter"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
            <div class="mentor-card">
              <div class="mentor-photo">
                <img src="assets/img/person/person-f-9.webp" alt="Naa Dromo Addo, career coach" class="img-fluid">
                <span class="badge badge-alt">Career</span>
              </div>
              <div class="mentor-info">
                <h3>Naa Dromo Addo</h3>
                <p class="role">Career Coach & Talent Partner</p>
                <ul>
                  <li><i class="bi bi-check2"></i> CV and LinkedIn clinics</li>
                  <li><i class="bi bi-check2"></i> Mock technical interviews</li>
                  <li><i class="bi bi-check2"></i> Internship placement support</li>
                </ul>
                <div class="mentor-links">
                  <a href="#" aria-label="Naa Dromo Addo on LinkedIn"><i class="bi bi-linkedin"></i></a>
                  <a href="#" aria-label="Naa Dromo Addo career resources"><i class="bi bi-briefcase"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mentorship Journey -->
    <section class="section mentorship-journey light-background">
      <div class="container" data-aos="fade-up">
        <div class="row gy-4 align-items-center">
          <div class="col-lg-5">
            <h2>Your Mentorship Journey</h2>
            <p>Our structured mentorship experience ensures every member receives consistent support while building independence.</p>
          </div>
          <div class="col-lg-7">
            <div class="journey-steps">
              <div class="journey-step">
                <span class="step-number">1</span>
                <div>
                  <h4>Match & Onboard</h4>
                  <p>Complete your learning profile, get matched with mentors across technical and career focus areas, and align on goals.</p>
                </div>
              </div>
              <div class="journey-step">
                <span class="step-number">2</span>
                <div>
                  <h4>Weekly Check-ins</h4>
                  <p>Attend individualized sessions, office hours, and async feedback cycles to stay unblocked and accountable.</p>
                </div>
              </div>
              <div class="journey-step">
                <span class="step-number">3</span>
                <div>
                  <h4>Project Collaborations</h4>
                  <p>Work side-by-side on real codebases, design critiques, or data pipelines with mentors reviewing each milestone.</p>
                </div>
              </div>
              <div class="journey-step">
                <span class="step-number">4</span>
                <div>
                  <h4>Career Launch</h4>
                  <p>Receive referrals, interview prep, and networking introductions as you transition into internships or full-time roles.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="section cta-section accent-background">
      <div class="container" data-aos="zoom-in">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <h2>Become a Mentor or Request Support</h2>
            <p>Whether you’re ready to mentor future technologists or seeking guidance for your own journey, KHODERS has a place for you.</p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="index.php?page=register" class="btn btn-light">Request a Mentor</a>
            <a href="index.php?page=contact" class="btn btn-outline-light ms-lg-3">Partner as Mentor</a>
          </div>
        </div>
      </div>
    </section>
<?php
$content = ob_get_clean();
echo render_page($content, $title, ['body_class' => 'instructors-page']);
?>
