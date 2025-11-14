<?php
/**
 * Generate PHP wrapper files for all HTML pages
 * This script creates PHP files that use the template system to display HTML content
 */

// Define the pages
$pages = [
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
    'terms-of-service' => 'Terms of Service - KHODERS WORLD'
];

// Generate wrapper files
foreach ($pages as $page => $title) {
    // Convert page name to title case for description
    $page_title_case = ucwords(str_replace('-', ' ', $page));
    
    $content = <<<PHP
<?php
/**
 * $page_title_case page
 */

// Include the template system
require_once 'includes/template.php';

// Get the HTML content from the file
\$html_file = 'pages/$page.html';
\$html_content = '';

if (file_exists(\$html_file)) {
    // Get the HTML content
    \$html_content = file_get_contents(\$html_file);
    
    // Extract the body content
    preg_match('/<body.*?>(.*?)<\\/body>/s', \$html_content, \$matches);
    \$html_content = \$matches[1] ?? \$html_content;
} else {
    // Fallback content
    \$html_content = '<div class="container mt-5"><h1>$page_title_case</h1><p>Content for $page_title_case page.</p></div>';
}

// Define meta data
\$meta_data = [
    'description' => '$page_title_case page for KHODERS coding club.',
    'keywords' => '$page, khoders, coding club, programming community'
];

// Render the page
echo render_page(\$html_content, '$title', \$meta_data);
?>
PHP;

    // Write the PHP file
    file_put_contents("../$page.php", $content);
    echo "Created $page.php\n";
}

echo "Done!\n";
?>
