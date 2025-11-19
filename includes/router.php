<?php
class SiteRouter {
    private static $pages = [];
    private static $titles = [];

    public static function init() {
        self::$pages = [
            'home' => 'pages/index.php',
            'about' => 'pages/about.php',
            'blog' => 'pages/blog.php',
            'blog-details' => 'pages/blog-details.php',
            'careers' => 'pages/careers.php',
            'code-of-conduct' => 'pages/code-of-conduct.php',
            'contact' => 'pages/contact.php',
            'courses' => 'pages/courses.php',
            'course-details' => 'pages/course-details.php',
            'dashboard' => 'pages/dashboard.php',
            'enroll' => 'pages/enroll.php',
            'events' => 'pages/events.php',
            'faq' => 'pages/faq.php',
            'instructors' => 'pages/instructors.php',
            'join-program' => 'pages/join-program.php',
            'login' => 'pages/login.php',
            'member-login' => 'pages/member-login.php',
            'member-logout' => 'pages/member-logout.php',
            'membership-tiers' => 'pages/membership-tiers.php',
            'mentor-profile' => 'pages/mentor-profile.php',
            'privacy-policy' => 'pages/privacy-policy.php',
            'profile' => 'pages/profile.php',
            'programs' => 'pages/programs.php',
            'program-details' => 'pages/program-details.php',
            'projects' => 'pages/projects.php',
            'register' => 'pages/join-program.php',
            'resources' => 'pages/resources.php',
            'services' => 'pages/services.php',
            'team' => 'pages/team.php',
            'terms-of-service' => 'pages/terms-of-service.php',
            '404' => 'pages/404.html'
        ];
        
        self::$titles = [
            'index' => 'KHODERS - Campus Coding Club',
            'blog' => 'Blog - KHODERS',
            'courses' => 'Courses - KHODERS',
            'programs' => 'Programs - KHODERS',
            'events' => 'Events - KHODERS',
            'projects' => 'Projects - KHODERS',
            'team' => 'Team - KHODERS',
            'enroll' => 'Enroll - KHODERS',
            'login' => 'Login - KHODERS',
            'register' => 'Join KHODERS'
        ];
    }

    public static function route($page = '') {
        if (empty(self::$pages)) self::init();
        if (empty($page)) $page = 'index';
        
        $page = preg_replace('/[^a-z0-9-]/', '', strtolower($page));
        
        if (!isset(self::$pages[$page]) && $page !== 'index') {
            header('HTTP/1.0 404 Not Found');
            $page = '404';
        }

        // Handle index page
        if ($page === 'index') {
            $filePath = 'pages/index.php';
            if (file_exists($filePath)) {
                include $filePath;
                return;
            }
        }

        // Handle other pages
        if (isset(self::$pages[$page])) {
            $filePath = self::$pages[$page];
            $realPath = realpath($filePath);
            $basePath = realpath('pages');
            
            // Security check to prevent directory traversal
            if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
                include $filePath;
                return;
            }
        }

        // Fallback 404
        header('HTTP/1.0 404 Not Found');
        include 'pages/404.html';
    }

    public static function getUrl($page) {
        if ($page === 'index' || $page === 'home') return 'index.php';
        if (empty(self::$pages)) self::init();
        return isset(self::$pages[$page]) ? 'index.php?page=' . urlencode($page) : 'index.php';
    }
}
