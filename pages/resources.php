<?php
/**
 * Resources Page - Learning Resources
 */

$page_title = 'Learning Resources - KHODERS';
$meta_data = [
    'description' => 'Access free learning resources for coding tutorials, tools, and career development from KHODERS.',
    'keywords' => 'learning resources, coding tutorials, web development, programming tools, career resources'
];

ob_start();
?>

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Learning Resources</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?php echo SiteRouter::getUrl('index'); ?>">Home</a></li>
        <li><a href="#">Resources</a></li>
        <li class="current">Learning Resources</li>
      </ol>
    </nav>
  </div>
</div><!-- End Page Title -->

<!-- Resources Section -->
<section id="resources" class="resources section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Free Learning Resources</h2>
    <p>Expand your coding skills with our curated collection of learning materials, tutorials, and tools</p>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up">
    <div class="row gy-4">
      
      <!-- Web Development Resources -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="100">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Web Development</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">HTML/CSS Fundamentals</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">JavaScript Essentials</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Responsive Design Guide</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Frontend Frameworks Overview</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Backend Development Basics</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="#" class="btn btn-outline-primary">View All</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Mobile Development Resources -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="200">
          <div class="card-header bg-success text-white">
            <h4 class="mb-0">Mobile Development</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Android Development Guide</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">iOS App Building Basics</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Flutter Framework Tutorial</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">React Native Essentials</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Mobile UI/UX Best Practices</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="#" class="btn btn-outline-success">View All</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Data Science Resources -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="300">
          <div class="card-header bg-info text-white">
            <h4 class="mb-0">Data Science</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Python for Data Analysis</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Introduction to ML</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Data Visualization Techniques</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">SQL Database Essentials</a></li>
              <li><i class="bi bi-file-earmark-code"></i> <a href="#">Big Data Processing Tools</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="#" class="btn btn-outline-info">View All</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Tools & Software -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="400">
          <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Development Tools</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-tools"></i> <a href="#">Git & GitHub Essentials</a></li>
              <li><i class="bi bi-tools"></i> <a href="#">VS Code Setup for Web Dev</a></li>
              <li><i class="bi bi-tools"></i> <a href="#">Package Managers Guide</a></li>
              <li><i class="bi bi-tools"></i> <a href="#">DevOps Basics</a></li>
              <li><i class="bi bi-tools"></i> <a href="#">Testing Frameworks Overview</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="#" class="btn btn-outline-warning">View All</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- UI/UX Design -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="500">
          <div class="card-header bg-danger text-white">
            <h4 class="mb-0">UI/UX Design</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-brush"></i> <a href="#">Design Principles</a></li>
              <li><i class="bi bi-brush"></i> <a href="#">Figma Essentials</a></li>
              <li><i class="bi bi-brush"></i> <a href="#">User Research Methods</a></li>
              <li><i class="bi bi-brush"></i> <a href="#">Wireframing Techniques</a></li>
              <li><i class="bi bi-brush"></i> <a href="#">Color Theory for Web</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="#" class="btn btn-outline-danger">View All</a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Career Development -->
      <div class="col-lg-4 col-md-6">
        <div class="card" data-aos="fade-up" data-aos-delay="600">
          <div class="card-header bg-secondary text-white">
            <h4 class="mb-0">Career Resources</h4>
          </div>
          <div class="card-body">
            <ul class="resource-list">
              <li><i class="bi bi-briefcase"></i> <a href="#">Tech Resume Templates</a></li>
              <li><i class="bi bi-briefcase"></i> <a href="#">Portfolio Building Guide</a></li>
              <li><i class="bi bi-briefcase"></i> <a href="#">Technical Interview Prep</a></li>
              <li><i class="bi bi-briefcase"></i> <a href="#">Remote Job Resources</a></li>
              <li><i class="bi bi-briefcase"></i> <a href="#">Networking for Developers</a></li>
            </ul>
            <div class="text-center mt-3">
              <a href="<?php echo SiteRouter::getUrl('careers'); ?>" class="btn btn-outline-secondary">View All</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- External Resources Section -->
    <div class="external-resources mt-5" data-aos="fade-up">
      <h3 class="text-center mb-4">Recommended External Resources</h3>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Resource</th>
                  <th>Type</th>
                  <th>Description</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><a href="https://www.freecodecamp.org/" target="_blank">freeCodeCamp</a></td>
                  <td>Learning Platform</td>
                  <td>Free coding courses and certifications</td>
                </tr>
                <tr>
                  <td><a href="https://www.w3schools.com/" target="_blank">W3Schools</a></td>
                  <td>Reference</td>
                  <td>Web development tutorials and reference</td>
                </tr>
                <tr>
                  <td><a href="https://developer.mozilla.org/" target="_blank">MDN Web Docs</a></td>
                  <td>Documentation</td>
                  <td>Comprehensive web technology documentation</td>
                </tr>
                <tr>
                  <td><a href="https://www.codecademy.com/" target="_blank">Codecademy</a></td>
                  <td>Learning Platform</td>
                  <td>Interactive coding courses with free tiers</td>
                </tr>
                <tr>
                  <td><a href="https://github.com/" target="_blank">GitHub</a></td>
                  <td>Repository</td>
                  <td>Open-source projects and code examples</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section><!-- /Resources Section -->

<?php
$html_content = ob_get_clean();

if (isset($_GET['page'])) {
    require_once __DIR__ . '/../includes/template.php';
    echo render_page($html_content, $page_title, $meta_data);
    exit;
}

echo $html_content;
?>
