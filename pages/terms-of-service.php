<?php
/**
 * Terms of Service Page
 */

$page_title = 'Terms of Service - KHODERS Campus Coding Community';
$meta_data = [
    'description' => 'Terms of Service for KHODERS campus coding club membership and use of services.',
    'keywords' => 'coding club terms, tech community agreement, service terms, member agreement'
];

ob_start();
?>

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Terms of Service</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?php echo SiteRouter::getUrl('index'); ?>">Home</a></li>
        <li><a href="#">Resources</a></li>
        <li class="current">Terms of Service</li>
      </ol>
    </nav>
  </div>
</div><!-- End Page Title -->

<!-- Terms of Service Section -->
<section id="terms-of-service" class="terms-of-service section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <div class="col-lg-8 mx-auto">

        <div class="mb-5">
          <h2>1. Introduction</h2>
          <p>Welcome to KHODERS ("Company," "we," "our," or "us"). These Terms of Service ("Terms," "ToS," or "Agreement") govern your use of the KHODERS website, services, and membership. By accessing or using KHODERS services, you agree to be bound by these Terms.</p>
        </div>

        <div class="mb-5">
          <h2>2. Definitions</h2>
          <ul>
            <li><strong>"Services"</strong> refers to all services, programs, events, and resources offered by KHODERS, including coding workshops, mentorship, project teams, and access to learning resources.</li>
            <li><strong>"Member"</strong> refers to any student at Kumasi Technical University who has joined KHODERS.</li>
            <li><strong>"Content"</strong> refers to any code, documents, materials, and information provided by KHODERS or members.</li>
          </ul>
        </div>

        <div class="mb-5">
          <h2>3. Eligibility</h2>
          <p>To become a member of KHODERS, you must:</p>
          <ul>
            <li>Be a currently enrolled student at Kumasi Technical University</li>
            <li>Be at least 18 years old (or have parental consent if under 18)</li>
            <li>Agree to comply with all applicable laws and these Terms</li>
            <li>Agree to abide by our Code of Conduct</li>
          </ul>
        </div>

        <div class="mb-5">
          <h2>4. Membership</h2>
          <p>Membership to KHODERS is FREE for all KTU students. Membership includes:</p>
          <ul>
            <li>Access to weekly coding sessions</li>
            <li>Participation in community events and projects</li>
            <li>Access to learning resources and mentorship</li>
            <li>Networking opportunities with industry professionals</li>
          </ul>
          <p>We reserve the right to suspend or terminate membership for violations of our Code of Conduct or these Terms.</p>
        </div>

        <div class="mb-5">
          <h2>5. Intellectual Property Rights</h2>
          <p>All content, materials, code samples, and resources provided by KHODERS are protected by intellectual property laws. You may use these materials for personal learning purposes only, with proper attribution to KHODERS.</p>
          <p>Code and materials you contribute to KHODERS projects must be original work or properly licensed. You grant KHODERS permission to use your contributions for educational and promotional purposes.</p>
        </div>

        <div class="mb-5">
          <h2>6. Acceptable Use Policy</h2>
          <p>You agree NOT to:</p>
          <ul>
            <li>Use KHODERS services for any illegal or unauthorized purpose</li>
            <li>Submit false, fraudulent, or misleading information</li>
            <li>Harass, abuse, or harm other members</li>
            <li>Plagiarize or claim credit for others' work</li>
            <li>Attempt to gain unauthorized access to systems or data</li>
            <li>Disrupt KHODERS services or events</li>
            <li>Violate any applicable laws or regulations</li>
          </ul>
        </div>

        <div class="mb-5">
          <h2>7. Limitation of Liability</h2>
          <p>TO THE FULLEST EXTENT PERMITTED BY LAW, KHODERS AND ITS MEMBERS, MENTORS, AND OFFICERS SHALL NOT BE LIABLE FOR:</p>
          <ul>
            <li>Any indirect, incidental, special, or consequential damages</li>
            <li>Loss of profits, revenue, data, or goodwill</li>
            <li>Any damages arising from your use or inability to use KHODERS services</li>
          </ul>
        </div>

        <div class="mb-5">
          <h2>8. Warranties Disclaimer</h2>
          <p>KHODERS SERVICES ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. WE MAKE NO WARRANTIES REGARDING THE ACCURACY, RELIABILITY, OR COMPLETENESS OF ANY CONTENT OR SERVICES PROVIDED.</p>
        </div>

        <div class="mb-5">
          <h2>9. Changes to Terms</h2>
          <p>We may update these Terms at any time. Changes will be posted on this page with an updated "Last Updated" date. Your continued use of KHODERS services following such changes constitutes your acceptance of the revised Terms.</p>
        </div>

        <div class="mb-5">
          <h2>10. Governing Law</h2>
          <p>These Terms shall be governed by and construed in accordance with the laws of Ghana, without regard to its conflict of law provisions.</p>
        </div>

        <div class="mb-5">
          <h2>11. Contact Us</h2>
          <p>If you have questions about these Terms of Service, please contact us at:</p>
          <p>
            <strong>KHODERS</strong><br>
            Email: <strong>info@khodersclub.com</strong><br>
            Phone: +233 50 123 4567<br>
            Kumasi Technical University, Kumasi, Ghana
          </p>
        </div>

        <div class="text-center my-5">
          <p class="mb-4">Last Updated: November 2024</p>
          <a href="<?php echo SiteRouter::getUrl('register'); ?>" class="btn btn-primary">Join Our Community</a>
        </div>

      </div>
    </div>
  </div>
</section><!-- /Terms of Service Section -->

<?php
$html_content = ob_get_clean();

if (isset($_GET['page'])) {
    require_once __DIR__ . '/../includes/template.php';
    echo render_page($html_content, $page_title, $meta_data);
    exit;
}

echo $html_content;
?>
