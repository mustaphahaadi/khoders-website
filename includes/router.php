<?php
class SiteRouter {
    private static $pages = [];
    private static $titles = [];

    public static function init() {
        self::$pages = [
            'about' => 'pages/about.html',
            'blog' => 'pages/blog.php',
            'blog-details' => 'pages/blog-details.php',
            'careers' => 'pages/careers.php',
            'code-of-conduct' => 'pages/conduct.php',
            'contact' => 'pages/contact.php',
            'courses' => 'pages/courses.php',
            'course-details' => 'pages/course-details.php',
            'enroll' => 'pages/enroll.php',
            'events' => 'pages/events.php',
            'faq' => 'pages/faq.php',
            'instructors' => 'pages/instructors.php',
            'join-program' => 'pages/join.php',
            'login' => 'pages/login.php',
            'membership-tiers' => 'pages/membership.php',
            'mentor-profile' => 'pages/mentor.php',
            'privacy-policy' => 'pages/privacy.php',
            'programs' => 'pages/programs.php',
            'program-details' => 'pages/program-details.php',
            'projects' => 'pages/projects.php',
            'register' => 'pages/register.php',
            'resources' => 'pages/resources.php',
            'services' => 'pages/services.php',
            'team' => 'pages/team.php',
            'terms-of-service' => 'pages/terms.php',
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
        
        $page = preg_replace('/[^a-z0-9-]/', '', strtolower($page));
        
        if (!isset(self::$pages[$page]) && $page !== 'index') {
            header('HTTP/1.0 404 Not Found');
            $page = '404';
        }

        $dynamicPages = ['events', 'team', 'projects', 'blog', 'blog-details', 'programs', 'program-details', 'courses', 'course-details', 'enroll', 'login', 'careers', 'code-of-conduct', 'faq', 'instructors', 'resources', 'services', 'privacy-policy', 'terms-of-service', 'join-program', 'membership-tiers', 'mentor-profile'];
        $html_content = '';

        if ($page === 'index') {
            $filePath = 'pages/index.html';
            $realPath = realpath($filePath);
            $basePath = realpath('pages');
            if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
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
            $realPath = realpath($filePath);
            $basePath = realpath('pages');
            if ($realPath && $basePath && strpos($realPath, $basePath) === 0 && file_exists($filePath)) {
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
