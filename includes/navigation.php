<?php
/**
 * Main Navigation - Khoders World
 */

require_once __DIR__ . '/router.php';
require_once __DIR__ . '/member-auth.php';

// Check if member is logged in
$isLoggedIn = MemberAuth::isLoggedIn();
$memberName = $isLoggedIn ? ($_SESSION['member_name'] ?? 'Member') : '';

function get_nav_links() {
    return [
        'home' => SiteRouter::getUrl('home'),
        'about' => SiteRouter::getUrl('about'),
        'courses' => SiteRouter::getUrl('courses'),
        'programs' => SiteRouter::getUrl('programs'),
        'projects' => SiteRouter::getUrl('projects'),
        'team' => SiteRouter::getUrl('team'),
        'events' => SiteRouter::getUrl('events'),
        'blog' => SiteRouter::getUrl('blog'),
        'contact' => SiteRouter::getUrl('contact'),
        'register' => SiteRouter::getUrl('register')
    ];
}
?>

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
        <a href="<?php echo get_nav_links()['home']; ?>" class="logo d-flex align-items-center me-auto">
            <img src="assets/img/khoders/logo.png" alt="Khoders World Logo" width="40" height="40">
            <h1 class="sitename">KHODERS WORLD</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="<?php echo get_nav_links()['home']; ?>">Home</a></li>
                <li><a href="<?php echo get_nav_links()['about']; ?>">About</a></li>
                <li><a href="<?php echo get_nav_links()['events']; ?>">Events</a></li>
                <li><a href="<?php echo get_nav_links()['courses']; ?>">Courses</a></li>
                <li><a href="<?php echo get_nav_links()['programs']; ?>">Programs</a></li>
                <li><a href="<?php echo get_nav_links()['projects']; ?>">Projects</a></li>
                <li><a href="<?php echo get_nav_links()['blog']; ?>">Blog</a></li>
                <li><a href="<?php echo get_nav_links()['team']; ?>">Team</a></li>
                <li><a href="<?php echo get_nav_links()['contact']; ?>">Contact</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <?php if ($isLoggedIn): ?>
          <!-- Logged In: Show Dashboard Dropdown -->
          <div class="dropdown">
            <button class="btn-getstarted dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars(explode(' ', $memberName)[0]); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
              <li><a class="dropdown-item" href="index.php?page=profile"><i class="bi bi-person"></i> Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="index.php?page=member-logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Not Logged In: Show Login + Join -->
          <a class="btn-getstarted me-2" href="index.php?page=member-login">
            <i class="bi bi-box-arrow-in-right"></i> Login
          </a>
          <a class="btn-getstarted" href="<?php echo get_nav_links()['register']; ?>">Join Now</a>
        <?php endif; ?>
    </div>
</header>
