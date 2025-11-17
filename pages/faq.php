<?php
/**
 * FAQ Page
 * Displays frequently asked questions about KHODERS
 */

// Set page metadata
$page_title = 'Frequently Asked Questions - KHODERS';
$meta_data = [
    'description' => 'Find answers to frequently asked questions about KHODERS, our programs, events, and how you can get involved.',
    'keywords' => 'coding club faq, programming community questions, student developers help, campus tech group, khoders faq'
];

ob_start();
?>

<!-- Page Title -->
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Frequently Asked Questions</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="<?php echo SiteRouter::getUrl('index'); ?>">Home</a></li>
        <li><a href="#">Resources</a></li>
        <li class="current">FAQ</li>
      </ol>
    </nav>
  </div>
</div><!-- End Page Title -->

<!-- FAQ Section -->
<section id="faq" class="faq section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="section-header text-center mb-5">
      <h2>Common Questions</h2>
      <p>Find answers to frequently asked questions about KHODERS, our programs, events, and how you can get involved.</p>
    </div>

    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="accordion" id="faqAccordion">
          
          <!-- General Questions -->
          <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
            <h3 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem1" aria-expanded="true" aria-controls="faqItem1">
                What is KHODERS?
              </button>
            </h3>
            <div id="faqItem1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>KHODERS is the premier campus coding club at Kumasi Technical University in Ghana. We are a community of student developers, designers, and tech enthusiasts who learn together, build projects, and help each other grow in the field of technology. Our mission is to bridge the gap between academic learning and practical software development skills.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item" data-aos="fade-up" data-aos-delay="250">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem2" aria-expanded="false" aria-controls="faqItem2">
                Who can join KHODERS?
              </button>
            </h3>
            <div id="faqItem2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>KHODERS welcomes all Kumasi Technical University students regardless of their program of study, year level, or prior coding experience. Whether you're a complete beginner curious about programming or an experienced developer looking to share your knowledge, there's a place for you in our community.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem3" aria-expanded="false" aria-controls="faqItem3">
                How much does membership cost?
              </button>
            </h3>
            <div id="faqItem3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Basic membership to KHODERS is free for all KTU students. This gives you access to our regular meetups, coding sessions, and online community. Some special events, workshops, or bootcamps may have a small fee to cover materials and resources, but we strive to keep costs minimal and offer scholarships when possible.</p>
              </div>
            </div>
          </div>

          <!-- Participation Questions -->
          <div class="accordion-item" data-aos="fade-up" data-aos-delay="350">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem4" aria-expanded="false" aria-controls="faqItem4">
                I'm a complete beginner. Can I still join?
              </button>
            </h3>
            <div id="faqItem4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Absolutely! We have dedicated programs designed specifically for beginners. Our mentors and senior members are committed to helping newcomers learn the basics of programming in a supportive environment. We recommend starting with our "Coding Fundamentals" workshops and gradually progressing to more advanced topics as you build your skills.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item" data-aos="fade-up" data-aos-delay="400">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem5" aria-expanded="false" aria-controls="faqItem5">
                What programming languages do you focus on?
              </button>
            </h3>
            <div id="faqItem5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>We cover a wide range of technologies based on industry demand and member interests. Currently, our main focus areas include:</p>
                <ul>
                  <li>Web Development: HTML, CSS, JavaScript, React, Node.js</li>
                  <li>Mobile Development: Flutter, React Native</li>
                  <li>Data Science: Python, R, TensorFlow, pandas</li>
                  <li>Backend: Java, Python, PHP, SQL, MongoDB</li>
                  <li>DevOps: Git, Docker, AWS basics</li>
                </ul>
                <p>We're always open to exploring new technologies based on member interests.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item" data-aos="fade-up" data-aos-delay="450">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem6" aria-expanded="false" aria-controls="faqItem6">
                How do I join a project team?
              </button>
            </h3>
            <div id="faqItem6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>Project teams are formed at the beginning of each semester. We announce open project opportunities through our website, social media, and during meetings. To join a project:</p>
                <ol>
                  <li>Attend our project kickoff event where team leads present project ideas</li>
                  <li>Fill out the project interest form indicating your skills and availability</li>
                  <li>Interview with project leads for team placement (if required)</li>
                </ol>
                <p>You can also propose your own project ideas if you have something specific you want to work on!</p>
              </div>
            </div>
          </div>

          <!-- Event & Resources Questions -->
          <div class="accordion-item" data-aos="fade-up" data-aos-delay="500">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem7" aria-expanded="false" aria-controls="faqItem7">
                How often do you organize events?
              </button>
            </h3>
            <div id="faqItem7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>KHODERS hosts:</p>
                <ul>
                  <li>Weekly coding sessions (every Wednesday)</li>
                  <li>Monthly tech talks featuring industry speakers</li>
                  <li>Semesterly hackathons (48-hour coding marathons)</li>
                  <li>Special workshops and bootcamps during breaks</li>
                  <li>Career fairs and networking events each semester</li>
                </ul>
                <p>Check our Events page or subscribe to our newsletter to stay updated on upcoming activities.</p>
              </div>
            </div>
          </div>

          <div class="accordion-item" data-aos="fade-up" data-aos-delay="550">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqItem8" aria-expanded="false" aria-controls="faqItem8">
                Do you provide job placement or internship opportunities?
              </button>
            </h3>
            <div id="faqItem8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <p>While we don't guarantee job placements, KHODERS has built strong relationships with local and international tech companies that regularly hire our members. We provide:</p>
                <ul>
                  <li>Resume and portfolio review sessions</li>
                  <li>Mock interview practice</li>
                  <li>Exclusive job postings from partner companies</li>
                  <li>Networking opportunities with industry professionals</li>
                  <li>Recommendation letters for outstanding members</li>
                </ul>
                <p>Many of our alumni have gone on to work at leading tech companies both locally and internationally.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="600">
          <p>Can't find the answer to your question? Feel free to reach out to us!</p>
          <a href="<?php echo SiteRouter::getUrl('contact'); ?>" class="btn btn-primary">Contact Us</a>
        </div>
      </div>
    </div>

  </div>
</section><!-- /FAQ Section -->

<?php
$html_content = ob_get_clean();

// If called via router, return the content
if (isset($_GET['page'])) {
    require_once __DIR__ . '/../includes/template.php';
    echo render_page($html_content, $page_title, $meta_data);
    exit;
}

echo $html_content;
?>
