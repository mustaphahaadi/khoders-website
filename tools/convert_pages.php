<?php
/**
 * Convert HTML pages to use the template system
 * 
 * This script reads the HTML files in the pages directory,
 * extracts the content between <body> and </body> tags,
 * and creates new PHP files that use the template system.
 */

// Define the pages directory
$pages_dir = '../pages/';

// Get all HTML files in the pages directory
$html_files = glob($pages_dir . '*.html');

// Loop through each HTML file
foreach ($html_files as $html_file) {
    // Get the filename without extension
    $filename = basename($html_file, '.html');
    
    // Read the HTML file
    $html = file_get_contents($html_file);
    
    // Extract the content between <body> and </body> tags
    preg_match('/<body.*?>(.*?)<\/body>/s', $html, $matches);
    $content = $matches[1] ?? '';
    
    // Create the PHP file content
    $php_content = <<<PHP
<?php
/**
 * $filename page
 */

// Include the template system
require_once 'includes/template.php';

// Define the page content
\$content = <<<HTML
$content
HTML;

// Render the page
echo render_page(\$content);
?>
PHP;
    
    // Write the PHP file
    file_put_contents("../$filename.php", $php_content);
    
    echo "Created $filename.php\n";
}

echo "Done!\n";
?>
