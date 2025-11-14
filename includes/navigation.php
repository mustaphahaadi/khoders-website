<?php
/**
 * Common navigation include file
 */

// Include the router if not already included
if (!class_exists('SiteRouter')) {
    require_once __DIR__ . '/router.php';
}

// Get the current page
$current_page = isset($_GET['page']) ? $_GET['page'] : 'index';
if ($current_page === 'index') {
    $current_page = 'home';
}

// Function to check if a page is active
function is_active($page) {
    global $current_page;
    return $current_page === $page ? 'active' : '';
}

// Function to get URL for a page
function get_page_url($page) {
    return SiteRouter::getUrl($page);
}
?>

<!-- Navigation -->
<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="<?php echo get_page_url('index'); ?>" class="logo d-flex align-items-center me-auto">
            <!-- Using KHODERS logo -->
            <img src="assets/img/khoders/logo.png" alt="KHODERS Logo" width="40" height="40">
            <h1 class="sitename">KHODERS WORLD</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="<?php echo get_page_url('index'); ?>" class="<?php echo is_active('index') || is_active('home') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo get_page_url('about'); ?>" class="<?php echo is_active('about') ? 'active' : ''; ?>">About</a></li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Learn</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_page_url('courses'); ?>" class="<?php echo is_active('courses') ? 'active' : ''; ?>">Programs</a></li>
                        <li><a href="<?php echo get_page_url('services'); ?>" class="<?php echo is_active('services') ? 'active' : ''; ?>">Member Services</a></li>
                        <li><a href="<?php echo get_page_url('instructors'); ?>" class="<?php echo is_active('instructors') ? 'active' : ''; ?>">Mentors</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Community</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_page_url('projects'); ?>" class="<?php echo is_active('projects') ? 'active' : ''; ?>">Projects</a></li>
                        <li><a href="<?php echo get_page_url('team'); ?>" class="<?php echo is_active('team') ? 'active' : ''; ?>">Leadership Team</a></li>
                        <li><a href="<?php echo get_page_url('events'); ?>" class="<?php echo is_active('events') ? 'active' : ''; ?>">Events</a></li>
                        <li><a href="<?php echo get_page_url('blog'); ?>" class="<?php echo is_active('blog') ? 'active' : ''; ?>">Blog</a></li>
                        <li><a href="<?php echo get_page_url('careers'); ?>" class="<?php echo is_active('careers') ? 'active' : ''; ?>">Careers</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Resources</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_page_url('resources'); ?>" class="<?php echo is_active('resources') ? 'active' : ''; ?>">Resource Library</a></li>
                        <li><a href="<?php echo get_page_url('faq'); ?>" class="<?php echo is_active('faq') ? 'active' : ''; ?>">FAQ</a></li>
                        <li><a href="<?php echo get_page_url('code-of-conduct'); ?>" class="<?php echo is_active('code-of-conduct') ? 'active' : ''; ?>">Code of Conduct</a></li>
                        <li><a href="<?php echo get_page_url('membership-tiers'); ?>" class="<?php echo is_active('membership-tiers') ? 'active' : ''; ?>">Membership Tiers</a></li>
                        <li><a href="<?php echo get_page_url('privacy-policy'); ?>" class="<?php echo is_active('privacy-policy') ? 'active' : ''; ?>">Privacy Policy</a></li>
                        <li><a href="<?php echo get_page_url('terms-of-service'); ?>" class="<?php echo is_active('terms-of-service') ? 'active' : ''; ?>">Terms of Service</a></li>
                    </ul>
                </li>
                <li class="desktop-only"><a href="<?php echo get_page_url('contact'); ?>" class="contact-link <?php echo is_active('contact') ? 'active' : ''; ?>"><span>Contact</span></a></li>
                <li class="mobile-only"><a href="<?php echo get_page_url('contact'); ?>" class="<?php echo is_active('contact') ? 'active' : ''; ?>">Contact</a></li>
                <li><a href="<?php echo get_page_url('register'); ?>" class="cta-nav-btn">Join Now</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="btn-getstarted" href="<?php echo get_page_url('register'); ?>">Join Now</a>

    </div>
</header>
