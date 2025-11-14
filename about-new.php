<?php
/**
 * About page
 */

// Include the template system
require_once 'includes/template.php';

// Define the page content
$content = <<<HTML
<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">About</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="index.php">Home</a></li>
        <li class="current">About</li>
      </ol>
    </nav>
  </div>
</div><!-- End Page Title -->

<!-- About Section -->
<section id="about" class="about section">

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row align-items-center">
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
        <img src="assets/img/education/education-square-2.webp" alt="About Us" class="img-fluid rounded-4">
      </div>
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
        <div class="about-content">
          <span class="subtitle">About Us</span>
          <h2>Empowering Student Developers Through Community and Mentorship</h2>
          <p>KHODERS was founded in 2017 at Kumasi Technical University with a simple mission: to help students learn coding in a collaborative, supportive environment. We believe that programming skills are best developed through hands-on practice, peer learning, and mentorship from experienced developers.</p>
          <div class="stats-row">
            <div class="stats-item">
              <span class="count">7</span>
              <p>Years Running</p>
            </div>
            <div class="stats-item">
              <span class="count">15+</span>
              <p>Industry Mentors</p>
            </div>
            <div class="stats-item">
              <span class="count">500+</span>
              <p>Student Members</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5 pt-4">
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="mission-card">
          <div class="icon-box">
            <i class="bi bi-bullseye"></i>
          </div>
          <h3>Our Mission</h3>
          <p>To assist beginners in entering the tech space smoothly by providing quality education, mentorship, and hands-on projects that develop practical programming skills and prepare students for tech careers.</p>
        </div>
      </div>
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
        <div class="mission-card">
          <div class="icon-box">
            <i class="bi bi-eye"></i>
          </div>
          <h3>Our Vision</h3>
          <p>To become the most impactful tech community in Ghana, creating a new generation of skilled developers who can build innovative solutions for local and global challenges.</p>
        </div>
      </div>
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
        <div class="mission-card">
          <div class="icon-box">
            <i class="bi bi-award"></i>
          </div>
          <h3>Our Values</h3>
          <p>Collaboration, continuous learning, inclusivity, practical application, and community support. We believe in learning by doing and growing together through shared knowledge and experiences.</p>
        </div>
      </div>
    </div>

    <div class="row mt-5 pt-3 align-items-center">
      <div class="col-lg-6 order-lg-2" data-aos="fade-up" data-aos-delay="300">
        <div class="achievements">
          <span class="subtitle">Why Choose KHODERS</span>
          <h2>More Than Just a Coding Club</h2>
          <p>At KHODERS, we go beyond teaching syntax. We focus on building complete developers with practical skills and professional connections.</p>
          <ul class="achievements-list">
            <li><i class="bi bi-check-circle-fill"></i> Hands-on project-based learning approach</li>
            <li><i class="bi bi-check-circle-fill"></i> Mentorship from industry professionals</li>
            <li><i class="bi bi-check-circle-fill"></i> Weekly coding sessions and hackathons</li>
            <li><i class="bi bi-check-circle-fill"></i> Job placement assistance and internship connections</li>
            <li><i class="bi bi-check-circle-fill"></i> Peer support system and collaborative environment</li>
          </ul>
          <a href="register.php" class="btn-explore">Join Our Community <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
      <div class="col-lg-6 order-lg-1" data-aos="fade-up" data-aos-delay="200">
        <div class="about-gallery">
          <div class="row g-3">
            <div class="col-6">
              <img src="assets/img/education/education-1.webp" alt="Campus Life" class="img-fluid rounded-3">
            </div>
            <div class="col-6">
              <img src="assets/img/education/students-3.webp" alt="Student Achievement" class="img-fluid rounded-3">
            </div>
            <div class="col-12 mt-3">
              <img src="assets/img/education/campus-8.webp" alt="Our Campus" class="img-fluid rounded-3">
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</section><!-- /About Section -->
HTML;

// Render the page
echo render_page($content, 'About - KHODERS WORLD');
?>
