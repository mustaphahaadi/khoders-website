<?php
require_once 'includes/template.php';
$title = 'Membership Tiers - KHODERS Campus Coding Community';
ob_start();
?>
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Membership Tiers</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Resources</a></li>
            <li class="current">Membership Tiers</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Pricing Section -->
    <section id="pricing" class="pricing section">

      <div class="container pricing-toggle-container" data-aos="fade-up" data-aos-delay="100">

        <!-- Membership Types Header -->
        <div class="section-header text-center mb-5">
          <h2>Choose Your Membership Level</h2>
          <p>Join our vibrant community of tech enthusiasts and future developers. All membership tiers are free for KTU students, with different levels of involvement and benefits.</p>
        </div>

        <!-- Pricing Plans -->
        <div class="row gy-4 justify-content-center">

          <!-- Basic Plan -->
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="pricing-item">
              <div class="pricing-header">
                <h6 class="pricing-category">Explorer</h6>
                <div class="price-wrap">
                  <h2 class="price">Free</h2>
                </div>
                <p class="pricing-description">For curious minds</p>
              </div>

              <div class="pricing-cta">
                <a href="index.php?page=register" class="btn btn-primary w-100">Join Now</a>
              </div>

              <div class="pricing-features">
                <h6>Explorer Member Benefits:</h6>
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> Access to introductory workshops</li>
                  <li><i class="bi bi-check"></i> Attend club meetings & tech talks</li>
                  <li><i class="bi bi-check"></i> Basic learning resources</li>
                  <li><i class="bi bi-check"></i> Join community discussion forum</li>
                  <li><i class="bi bi-check"></i> Networking opportunities</li>
                  <li><i class="bi bi-check"></i> Campus tech events access</li>
                  <li><i class="bi bi-x"></i> Project collaboration</li>
                  <li><i class="bi bi-x"></i> One-on-one mentorship</li>
                  <li><i class="bi bi-x"></i> Leadership opportunities</li>
                </ul>
              </div>
            </div>
          </div><!-- End Basic Plan -->

          <!-- Plus Plan -->
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="pricing-item">
              <div class="pricing-header">
                <h6 class="pricing-category">Builder</h6>
                <div class="price-wrap">
                  <h2 class="price">Free</h2>
                </div>
                <p class="pricing-description">For project contributors</p>
              </div>

              <div class="pricing-cta">
                <a href="index.php?page=register" class="btn btn-primary w-100">Join Now</a>
              </div>

              <div class="pricing-features">
                <h6>Everything from <strong>Explorer</strong>, plus:</h6>
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> Project collaboration opportunities</li>
                  <li><i class="bi bi-check"></i> Advanced workshops & tutorials</li>
                  <li><i class="bi bi-check"></i> Access to coding challenges</li>
                  <li><i class="bi bi-check"></i> Group mentorship sessions</li>
                  <li><i class="bi bi-check"></i> Access to project resources</li>
                  <li><i class="bi bi-check"></i> Certificate of participation</li>
                  <li><i class="bi bi-check"></i> Resume building assistance</li>
                  <li><i class="bi bi-check"></i> GitHub organization membership</li>
                  <li><i class="bi bi-x"></i> Leadership of club projects</li>
                  <li><i class="bi bi-x"></i> One-on-one career guidance</li>
                </ul>
              </div>
            </div>
          </div><!-- End Plus Plan -->

          <!-- Business Plan -->
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="pricing-item popular">
              <div class="popular-badge">Most Popular</div>
              <div class="pricing-header">
                <h6 class="pricing-category">Creator</h6>
                <div class="price-wrap">
                  <h2 class="price">Free</h2>
                </div>
                <p class="pricing-description">For active contributors</p>
              </div>

              <div class="pricing-cta">
                <a href="index.php?page=register" class="btn btn-primary w-100">Join Now</a>
              </div>

              <div class="pricing-features">
                <h6>Everything in <strong>Builder</strong>, plus:</h6>
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> <span class="feature-highlight">One-on-one mentorship</span></li>
                  <li><i class="bi bi-check"></i> Lead club projects & initiatives</li>
                  <li><i class="bi bi-check"></i> Teach workshops & sessions</li>
                  <li><i class="bi bi-check"></i> Personalized learning path</li>
                  <li><i class="bi bi-check"></i> Industry connection opportunities</li>
                  <li><i class="bi bi-check"></i> Priority access to tech events</li>
                  <li><i class="bi bi-check"></i> Portfolio review sessions</li>
                  <li><i class="bi bi-check"></i> Mock interview practice</li>
                  <li><i class="bi bi-check"></i> Leadership skill development</li>
                  <li><i class="bi bi-check"></i> Tech conference opportunities</li>
                </ul>
              </div>
            </div>
          </div><!-- End Business Plan -->

          <!-- Enterprise Plan -->
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="pricing-item">
              <div class="pricing-header">
                <h6 class="pricing-category">Ambassador</h6>
                <div class="price-wrap">
                  <h2 class="price">Application</h2>
                </div>
                <p class="pricing-description">For club leadership</p>
              </div>

              <div class="pricing-cta">
                <a href="index.php?page=contact" class="btn btn-primary w-100">Apply Now</a>
              </div>

              <div class="pricing-features">
                <h6>Everything in <strong>Creator</strong>, plus:</h6>
                <ul class="feature-list">
                  <li><i class="bi bi-check"></i> Club leadership position</li>
                  <li><i class="bi bi-check"></i> Represent KHODERS externally</li>
                  <li><i class="bi bi-check"></i> Access to faculty resources</li>
                  <li><i class="bi bi-check"></i> Design community initiatives</li>
                  <li><i class="bi bi-check"></i> Industry partnership involvement</li>
                  <li><i class="bi bi-check"></i> Direct industry connections</li>
                  <li><i class="bi bi-check"></i> Shape club's strategic direction</li>
                </ul>
              </div>
            </div>
          </div><!-- End Enterprise Plan -->

        </div>

      </div>

    </section><!-- /Pricing Section -->
<?php
$content = ob_get_clean();
echo render_page($content, $title, ['body_class' => 'pricing-page membership-page']);
?>
