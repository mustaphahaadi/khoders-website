<?php
if (!function_exists('get_nav_links')) {
    function get_nav_links() {
        return [
            'home' => 'index.php',
            'about' => 'index.php?page=about',
            'courses' => 'index.php?page=courses',
            'services' => 'index.php?page=services',
            'instructors' => 'index.php?page=instructors',
            'projects' => 'index.php?page=projects',
            'team' => 'index.php?page=team',
            'events' => 'index.php?page=events',
            'blog' => 'index.php?page=blog',
            'careers' => 'index.php?page=careers',
            'resources' => 'index.php?page=resources',
            'faq' => 'index.php?page=faq',
            'conduct' => 'index.php?page=code-of-conduct',
            'membership' => 'index.php?page=membership-tiers',
            'privacy' => 'index.php?page=privacy-policy',
            'terms' => 'index.php?page=terms-of-service',
            'contact' => 'index.php?page=contact',
            'register' => 'index.php?page=register'
        ];
    }
}
?>

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
        <a href="<?php echo get_nav_links()['home']; ?>" class="logo d-flex align-items-center me-auto">
            <img src="assets/img/khoders/logo.png" alt="KHODERS Logo" width="40" height="40">
            <h1 class="sitename">KHODERS WORLD</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="<?php echo get_nav_links()['home']; ?>">Home</a></li>
                <li><a href="<?php echo get_nav_links()['about']; ?>">About</a></li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Learn</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_nav_links()['courses']; ?>">Courses</a></li>
                        <li><a href="<?php echo get_nav_links()['services']; ?>">Member Services</a></li>
                        <li><a href="<?php echo get_nav_links()['instructors']; ?>">Mentors</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Community</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_nav_links()['projects']; ?>">Projects</a></li>
                        <li><a href="<?php echo get_nav_links()['team']; ?>">Leadership Team</a></li>
                        <li><a href="<?php echo get_nav_links()['events']; ?>">Events</a></li>
                        <li><a href="<?php echo get_nav_links()['blog']; ?>">Blog</a></li>
                        <li><a href="<?php echo get_nav_links()['careers']; ?>">Careers</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <span>Resources</span>
                        <i class="bi bi-chevron-down toggle-dropdown"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo get_nav_links()['resources']; ?>">Resource Library</a></li>
                        <li><a href="<?php echo get_nav_links()['faq']; ?>">FAQ</a></li>
                        <li><a href="<?php echo get_nav_links()['conduct']; ?>">Code of Conduct</a></li>
                        <li><a href="<?php echo get_nav_links()['membership']; ?>">Membership Tiers</a></li>
                        <li><a href="<?php echo get_nav_links()['privacy']; ?>">Privacy Policy</a></li>
                        <li><a href="<?php echo get_nav_links()['terms']; ?>">Terms of Service</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo get_nav_links()['contact']; ?>">Contact</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="btn-getstarted" href="<?php echo get_nav_links()['register']; ?>">Join Now</a>
    </div>
</header>
