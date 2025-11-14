<?php
/**
 * Generate PHP wrapper files for all HTML pages
 */

// Define the pages
$pages = [
    'about',
    'blog',
    'blog-details',
    'careers',
    'code-of-conduct',
    'contact',
    'courses',
    'events',
    'faq',
    'instructors',
    'join-program',
    'membership-tiers',
    'mentor-profile',
    'privacy-policy',
    'program-details',
    'projects',
    'register',
    'resources',
    'services',
    'team',
    'terms-of-service'
];

// Generate wrapper files
foreach ($pages as $page) {
    $content = "<?php\nrequire_once 'includes/router.php';\nSiteRouter::route('$page');\n?>";
    file_put_contents("../$page.php", $content);
    echo "Created $page.php\n";
}

echo "Done!\n";
?>
