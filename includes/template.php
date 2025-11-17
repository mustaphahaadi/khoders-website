<?php
/**
 * Page template
 * 
 * This file serves as a wrapper for HTML content, adding the common header,
 * navigation, and footer to create a complete page.
 * 
 * @param string $content The HTML content to display
 * @param string $page_title Optional page title override
 * @param array $meta_data Optional additional meta data
 */
function render_page($content, $page_title = '', $meta_data = []) {
    // Start output buffering
    ob_start();
    
    // Set page title for header.php to use
    global $title, $meta_description, $meta_keywords;
    
    // Set title if provided
    if (!empty($page_title)) {
        $title = $page_title;
    }
    
    // Set meta data if provided
    if (isset($meta_data['description'])) {
        $meta_description = $meta_data['description'];
    }
    
    if (isset($meta_data['keywords'])) {
        $meta_keywords = $meta_data['keywords'];
    }
    
    // Include header
    include 'header.php';
    
    // Include navigation
    $nav_file = 'includes/header/nav.html';
    if (file_exists($nav_file)) {
        include $nav_file;
    }
    
    // Output the content
    echo $content;
    
    // Include footer
    include 'footer.php';
    
    // Return the complete page
    return ob_get_clean();
}
?>
