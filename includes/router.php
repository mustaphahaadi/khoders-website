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
        // Check for .php first, then fall back to .html
        self::$pages = [
            'about' => file_exists('pages/about.php') ? 'pages/about.php' : 'pages/about.html',
            'blog' => file_exists('pages/blog.php') ? 'pages/blog.php' : 'pages/blog.html',
            'blog-details' => file_exists('pages/blog-details.php') ? 'pages/blog-details.php' : 'pages/blog-details.html',
            'careers' => file_exists('pages/careers.php') ? 'pages/careers.php' : 'pages/careers.html',
            'code-of-conduct' => file_exists('pages/code-of-conduct.php') ? 'pages/code-of-conduct.php' : 'pages/code-of-conduct.html',
            'contact' => file_exists('pages/contact.php') ? 'pages/contact.php' : 'pages/contact.html',
            'courses' => file_exists('pages/courses.php') ? 'pages/courses.php' : 'pages/courses.html',
            'events' => file_exists('pages/events.php') ? 'pages/events.php' : 'pages/events.html',
            'faq' => file_exists('pages/faq.php') ? 'pages/faq.php' : 'pages/faq.html',
            'instructors' => file_exists('pages/instructors.php') ? 'pages/instructors.php' : 'pages/instructors.html',
            'join-program' => file_exists('pages/join-program.php') ? 'pages/join-program.php' : 'pages/join-program.html',
            'membership-tiers' => file_exists('pages/membership-tiers.php') ? 'pages/membership-tiers.php' : 'pages/membership-tiers.html',
            'mentor-profile' => file_exists('pages/mentor-profile.php') ? 'pages/mentor-profile.php' : 'pages/mentor-profile.html',
            'privacy-policy' => file_exists('pages/privacy-policy.php') ? 'pages/privacy-policy.php' : 'pages/privacy-policy.html',
            'program-details' => file_exists('pages/program-details.php') ? 'pages/program-details.php' : 'pages/program-details.html',
            'projects' => file_exists('pages/projects.php') ? 'pages/projects.php' : 'pages/projects.html',
            'register' => file_exists('pages/register.php') ? 'pages/register.php' : 'pages/register.html',
            'resources' => file_exists('pages/resources.php') ? 'pages/resources.php' : 'pages/resources.html',
            'services' => file_exists('pages/services.php') ? 'pages/services.php' : 'pages/services.html',
            'team' => file_exists('pages/team.php') ? 'pages/team.php' : 'pages/team.html',
            'terms-of-service' => file_exists('pages/terms-of-service.php') ? 'pages/terms-of-service.php' : 'pages/terms-of-service.html',
            '404' => file_exists('pages/404.php') ? 'pages/404.php' : 'pages/404.html'
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

        // Pages that load from database
        $dynamicPages = ['events', 'team', 'projects'];
        $pageData = [];
        $html_content = '';

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
        } elseif (in_array($page, $dynamicPages) && isset(self::$pages[$page])) {
            // Load dynamic content from API
            $apiFile = 'api/' . $page . '-list.php';
            if (file_exists($apiFile)) {
                ob_start();
                include $apiFile;
                $apiResponse = ob_get_clean();
                $apiData = json_decode($apiResponse, true);
                
                if ($apiData && $apiData['success']) {
                    $pageData = $apiData['data'] ?? [];
                    $templateFile = 'pages/' . $page . '-template.php';
                    
                    if (file_exists($templateFile)) {
                        ob_start();
                        include $templateFile;
                        $html_content = ob_get_clean();
                    } else {
                        // Fallback to static HTML
                        $pageFile = self::$pages[$page];
                        if (file_exists($pageFile)) {
                            $html_content = file_get_contents($pageFile);
                            if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                                $html_content = $matches[1];
                            }
                        }
                    }
                } else {
                    // API failed, use static HTML
                    $pageFile = self::$pages[$page];
                    if (file_exists($pageFile)) {
                        $html_content = file_get_contents($pageFile);
                        if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
                            $html_content = $matches[1];
                        }
                    }
                }
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
