<?php
require_once 'includes/template.php';
$title = 'Code of Conduct - KHODERS Campus Coding Community';
ob_start();
?>
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Code of Conduct</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Code of Conduct</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Code of Conduct Section -->
    <section id="code-of-conduct" class="code-of-conduct section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        
        <div class="row justify-content-center">
          <div class="col-lg-10">
            
            <div class="conduct-intro mb-5">
              <p class="lead">KHODERS is dedicated to providing a harassment-free experience for everyone, regardless of gender, gender identity and expression, sexual orientation, disability, physical appearance, body size, race, age, or religion. We do not tolerate harassment of participants in any form.</p>
              <p>This Code of Conduct applies to all KHODERS spaces, including our physical meetups, online communities (Discord, WhatsApp, etc.), workshops, and events, both online and offline. Anyone who violates this Code of Conduct may be sanctioned or expelled from these spaces at the discretion of the administrators.</p>
            </div>

            <div class="conduct-content">
              <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body p-4">
                  <h3 class="card-title h4 text-primary mb-3">Our Standards</h3>
                  <p>Examples of behavior that contributes to creating a positive environment include:</p>
                  <ul>
                    <li>Using welcoming and inclusive language</li>
                    <li>Being respectful of differing viewpoints and experiences</li>
                    <li>Gracefully accepting constructive criticism</li>
                    <li>Focusing on what is best for the community</li>
                    <li>Showing empathy towards other community members</li>
                  </ul>
                  
                  <p class="mt-4">Examples of unacceptable behavior by participants include:</p>
                  <ul>
                    <li>The use of sexualized language or imagery and unwelcome sexual attention or advances</li>
                    <li>Trolling, insulting/derogatory comments, and personal or political attacks</li>
                    <li>Public or private harassment</li>
                    <li>Publishing others' private information, such as a physical or electronic address, without explicit permission</li>
                    <li>Other conduct which could reasonably be considered inappropriate in a professional setting</li>
                  </ul>
                </div>
              </div>

              <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-body p-4">
                  <h3 class="card-title h4 text-primary mb-3">Enforcement Responsibilities</h3>
                  <p>Community leaders are responsible for clarifying and enforcing our standards of acceptable behavior and will take appropriate and fair corrective action in response to any behavior that they deem inappropriate, threatening, offensive, or harmful.</p>
                  <p>Community leaders have the right and responsibility to remove, edit, or reject comments, commits, code, wiki edits, issues, and other contributions that are not aligned to this Code of Conduct, and will communicate reasons for moderation decisions when appropriate.</p>
                </div>
              </div>

              <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body p-4">
                  <h3 class="card-title h4 text-primary mb-3">Scope</h3>
                  <p>This Code of Conduct applies within all community spaces, and also applies when an individual is officially representing the community in public spaces. Examples of representing our community include using an official e-mail address, posting via an official social media account, or acting as an appointed representative at an online or offline event.</p>
                </div>
              </div>

              <div class="card border-0 shadow-sm mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="card-body p-4">
                  <h3 class="card-title h4 text-primary mb-3">Enforcement</h3>
                  <p>Instances of abusive, harassing, or otherwise unacceptable behavior may be reported to the community leaders responsible for enforcement at <a href="mailto:conduct@khoders.com">conduct@khoders.com</a>. All complaints will be reviewed and investigated promptly and fairly.</p>
                  <p>All community leaders are obligated to respect the privacy and security of the reporter of any incident.</p>
                  
                  <h5 class="mt-4">Enforcement Guidelines</h5>
                  <p>Community leaders will follow these Community Impact Guidelines in determining the consequences for any action they deem in violation of this Code of Conduct:</p>
                  
                  <ol>
                    <li class="mb-2"><strong>Correction:</strong> A private conversation with the community member to clarify the violation and explain why it was inappropriate.</li>
                    <li class="mb-2"><strong>Warning:</strong> A formal warning with consequences for continued behavior.</li>
                    <li class="mb-2"><strong>Temporary Ban:</strong> A temporary ban from community spaces for a specified period of time.</li>
                    <li><strong>Permanent Ban:</strong> A permanent ban from all community spaces and events.</li>
                  </ol>
                </div>
              </div>
              
              <div class="alert alert-info" role="alert" data-aos="fade-up" data-aos-delay="600">
                <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Attribution</h5>
                <p class="mb-0">This Code of Conduct is adapted from the <a href="https://www.contributor-covenant.org/" target="_blank" rel="noopener noreferrer">Contributor Covenant</a>, version 2.1.</p>
              </div>

            </div>
          </div>
        </div>

      </div>
    </section><!-- /Code of Conduct Section -->
<?php
$content = ob_get_clean();
echo render_page($content, $title, ['body_class' => 'conduct-page']);
?>
