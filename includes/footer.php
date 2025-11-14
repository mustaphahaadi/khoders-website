<?php
/**
 * Common footer include file
 */

// Include the router if not already included
if (!class_exists('SiteRouter')) {
    require_once __DIR__ . '/router.php';
}

// Function to get URL for a page if not already defined
if (!function_exists('get_page_url')) {
    function get_page_url($page) {
        return SiteRouter::getUrl($page);
    }
}
?>

<!-- Footer -->
<footer id="footer" class="footer accent-background">

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-5 col-md-12 footer-about">
                <a href="<?php echo get_page_url('index'); ?>" class="logo d-flex align-items-center">
                    <img src="assets/img/khoders/logo.png" alt="KHODERS Logo" width="40" height="40" class="me-2">
                    <span class="sitename">KHODERS</span>
                </a>
                <p>KHODERS WORLD is the premier campus coding club at Kumasi Technical University, offering practical programming education, mentorship, and career support to students of all skill levels.</p>
                <div class="social-links d-flex mt-4">
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-6 footer-links">
                <h4>Useful Links</h4>
                <ul>
                    <li><a href="<?php echo get_page_url('index'); ?>">Home</a></li>
                    <li><a href="<?php echo get_page_url('about'); ?>">About us</a></li>
                    <li><a href="<?php echo get_page_url('services'); ?>">Services</a></li>
                    <li><a href="<?php echo get_page_url('terms-of-service'); ?>">Terms of service</a></li>
                    <li><a href="<?php echo get_page_url('privacy-policy'); ?>">Privacy policy</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6 footer-links">
                <h4>Our Programs</h4>
                <ul>
                    <li><a href="<?php echo get_page_url('courses'); ?>">Web Development</a></li>
                    <li><a href="<?php echo get_page_url('courses'); ?>">Mobile Development</a></li>
                    <li><a href="<?php echo get_page_url('courses'); ?>">Data Science</a></li>
                    <li><a href="<?php echo get_page_url('courses'); ?>">UI/UX Design</a></li>
                    <li><a href="<?php echo get_page_url('courses'); ?>">Cloud Computing</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                <h4>Contact Us</h4>
                <p>Kumasi Technical University</p>
                <p>Kumasi, Ghana</p>
                <p>West Africa</p>
                <p class="mt-4"><strong>Phone:</strong> <span>+233 50 123 4567</span></p>
                <p><strong>Email:</strong> <span>info@khodersclub.com</span></p>
            </div>

        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>&copy; <span>Copyright</span> <strong class="px-1 sitename">Khoders World</strong> <span>All Rights Reserved</span></p>
    </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>
