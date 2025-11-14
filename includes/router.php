<?php
/**
 * KHODERS WORLD Website Router
 * Simple router for the main website to handle URL routing and page management
 */

class SiteRouter {
    private static $pages = [];
    private static $baseUrl = '';
    private static $titles = [];
    private static $meta = [];

    /**
     * Initialize the router with available pages
     */
    public static function init() {
        // Define available pages with their HTML source files
        self::$pages = [
            'about' => 'pages/about.html',
            'blog' => 'pages/blog.html',
            'blog-details' => 'pages/blog-details.html',
            'careers' => 'pages/careers.html',
            'code-of-conduct' => 'pages/code-of-conduct.html',
            'contact' => 'pages/contact.html',
            'courses' => 'pages/courses.html',
            'events' => 'pages/events.html',
            'faq' => 'pages/faq.html',
            'instructors' => 'pages/instructors.html',
            'join-program' => 'pages/join-program.html',
            'membership-tiers' => 'pages/membership-tiers.html',
            'mentor-profile' => 'pages/mentor-profile.html',
            'privacy-policy' => 'pages/privacy-policy.html',
            'program-details' => 'pages/program-details.html',
            'projects' => 'pages/projects.html',
            'register' => 'pages/register.html',
            'resources' => 'pages/resources.html',
            'services' => 'pages/services.html',
            'team' => 'pages/team.html',
            'terms-of-service' => 'pages/terms-of-service.html',
            '404' => 'pages/404.html'
        ];
        
        // Define page titles
        self::$titles = [
            'index' => 'KHODERS - Campus Coding Club',
            'about' => 'About - KHODERS WORLD',
            'blog' => 'Blog - KHODERS WORLD',
            'blog-details' => 'Blog Details - KHODERS WORLD',
            'careers' => 'Careers - KHODERS WORLD',
            'code-of-conduct' => 'Code of Conduct - KHODERS WORLD',
            'contact' => 'Contact Us - KHODERS WORLD',
            'courses' => 'Courses - KHODERS WORLD',
            'events' => 'Events - KHODERS WORLD',
            'faq' => 'FAQ - KHODERS WORLD',
            'instructors' => 'Instructors - KHODERS WORLD',
            'join-program' => 'Join Our Program - KHODERS WORLD',
            'membership-tiers' => 'Membership Tiers - KHODERS WORLD',
            'mentor-profile' => 'Mentor Profile - KHODERS WORLD',
            'privacy-policy' => 'Privacy Policy - KHODERS WORLD',
            'program-details' => 'Program Details - KHODERS WORLD',
            'projects' => 'Projects - KHODERS WORLD',
            'register' => 'Register - KHODERS WORLD',
            'resources' => 'Resources - KHODERS WORLD',
            'services' => 'Services - KHODERS WORLD',
            'team' => 'Team - KHODERS WORLD',
            'terms-of-service' => 'Terms of Service - KHODERS WORLD',
            '404' => 'Page Not Found - KHODERS WORLD'
        ];
        
        // Define meta descriptions
        self::$meta = [
            'index' => [
                'description' => 'KHODERS is a campus coding club dedicated to fostering programming skills and technological innovation among students',
                'keywords' => 'coding, programming, tech, campus, club, KHODERS, Ghana'
            ],
            // Add more meta data for other pages as needed
        ];
    }

    /**
     * Route to the appropriate page
     */
    public static function route($page = '') {
        // Include the template system
        require_once 'includes/template.php';
        
        // Initialize if not already done
        if (empty(self::$pages)) {
            self::init();
        }

        // Default to index if no page specified
        if (empty($page)) {
            $page = 'index';
        }

        // Check if page exists
        if ($page === 'index') {
            // Handle index page specially
            if (file_exists('pages/index.html')) {
                $html_content = file_get_contents('pages/index.html');
                
                // Prefer extracting the main content to avoid duplicate headers/nav/footers
                if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                    $html_content = $matches[1];
                } else {
                    // Fallback to body content
                    preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
                    $html_content = $matches[1] ?? $html_content;
                }
            } else {
                // Fallback to a default home page
                $html_content = '<div class="container mt-5"><h1>Welcome to KHODERS WORLD</h1><p>The premier campus coding club.</p></div>';
            }
        } elseif (isset(self::$pages[$page]) && file_exists(self::$pages[$page])) {
            // Get the HTML content from the file
            $html_content = file_get_contents(self::$pages[$page]);
            
            // Prefer extracting the main content to avoid duplicate headers/nav/footers
            if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                $html_content = $matches[1];
            } else {
                // Fallback to body content
                preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
                $html_content = $matches[1] ?? $html_content;
            }
        } else {
            // Page not found, show 404
            header('HTTP/1.0 404 Not Found');
            if (isset(self::$pages['404']) && file_exists(self::$pages['404'])) {
                $html_content = file_get_contents(self::$pages['404']);
                if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                    $html_content = $matches[1];
                } else {
                    preg_match('/<body.*?>(.*?)<\/body>/s', $html_content, $matches);
                    $html_content = $matches[1] ?? $html_content;
                }
            } else {
                // Fallback 404 content
                $html_content = '<div class="container mt-5"><h1>404 - Page Not Found</h1><p>The page you requested could not be found.</p></div>';
            }
            $page = '404';
        }
        
        // Get the page title
        $title = self::$titles[$page] ?? ucwords(str_replace('-', ' ', $page)) . ' - KHODERS WORLD';
        
        // Get meta data
        $meta_data = self::$meta[$page] ?? [];
        
        // Render the page using the template system
        echo render_page($html_content, $title, $meta_data);
    }

    /**
     * Get URL for a page
     */
    public static function getUrl($page) {
        // Check if it's the index page
        if ($page === 'index' || $page === 'home') {
            return 'index.php';
        }
        
        // Initialize if not already done
        if (empty(self::$pages)) {
            self::init();
        }

        // Check if page exists in our routing table
        if (isset(self::$pages[$page])) {
            // Use clean URLs for better SEO and user experience
            return 'index.php?page=' . urlencode($page);
        } else {
            return 'index.php'; // Default to index
        }
    }
}
