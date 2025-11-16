<?php
class SiteRouter {
    private static $pages = [];
    private static $titles = [];

    public static function init() {
        self::$pages = [
            'about' => 'pages/about.html',
            'blog' => 'pages/blog.php',
            'blog-details' => 'pages/blog-details.php',
            'careers' => 'pages/careers.html',
            'code-of-conduct' => 'pages/code-of-conduct.html',
            'contact' => 'pages/contact.php',
            'courses' => 'pages/courses.php',
            'course-details' => 'pages/course-details.php',
            'enroll' => 'pages/enroll.php',
            'events' => 'pages/events.php',
            'faq' => 'pages/faq.html',
            'instructors' => 'pages/instructors.html',
            'join-program' => 'pages/join-program.html',
            'login' => 'pages/login.php',
            'membership-tiers' => 'pages/membership-tiers.html',
            'mentor-profile' => 'pages/mentor-profile.html',
            'privacy-policy' => 'pages/privacy-policy.html',
            'programs' => 'pages/programs.php',
            'program-details' => 'pages/program-details.php',
            'projects' => 'pages/projects.php',
            'register' => 'pages/register.php',
            'resources' => 'pages/resources.html',
            'services' => 'pages/services.html',
            'team' => 'pages/team.php',
            'terms-of-service' => 'pages/terms-of-service.html',
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
        require_once 'includes/template.php';
        
        if (empty(self::$pages)) self::init();
        if (empty($page)) $page = 'index';
        
        // Sanitize page parameter to prevent path traversal
        $page = preg_replace('/[^a-z0-9-]/', '', strtolower($page));

        $dynamicPages = ['events', 'team', 'projects', 'blog', 'blog-details', 'programs', 'program-details', 'courses', 'course-details', 'enroll', 'login'];
        $html_content = '';

        if ($page === 'index') {
            $filePath = 'pages/index.html';
            if (file_exists($filePath) && realpath($filePath) === realpath('pages/index.html')) {
                $html_content = file_get_contents($filePath);
                if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                    $html_content = $matches[1];
                }
            }
        } elseif (in_array($page, $dynamicPages) && isset(self::$pages[$page])) {
            $filePath = self::$pages[$page];
            $realPath = realpath($filePath);
            $basePath = realpath('pages');
            if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
                ob_start();
                include $filePath;
                $html_content = ob_get_clean();
            }
        } elseif (isset(self::$pages[$page])) {
            $filePath = self::$pages[$page];
            $realPath = realpath($filePath);
            $basePath = realpath('pages');
            if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
                if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                    ob_start();
                    include $filePath;
                    return;
                }
                $html_content = file_get_contents($filePath);
            if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                $html_content = $matches[1];
            }
            }
        }
        
        if (empty($html_content)) {
            header('HTTP/1.0 404 Not Found');
            $filePath = 'pages/404.html';
            if (file_exists($filePath) && realpath($filePath) === realpath('pages/404.html')) {
                $html_content = file_get_contents($filePath);
            }
            if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                $html_content = $matches[1];
            }
            $page = '404';
        }
        
        $title = self::$titles[$page] ?? ucwords(str_replace('-', ' ', $page)) . ' - KHODERS';
        echo render_page($html_content, $title, []);
    }

    public static function getUrl($page) {
        if ($page === 'index' || $page === 'home') return 'index.php';
        if (empty(self::$pages)) self::init();
        return isset(self::$pages[$page]) ? 'index.php?page=' . urlencode($page) : 'index.php';
    }
}
