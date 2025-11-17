<?php
$file = __DIR__ . '/terms-of-service.html';
if (file_exists($file)) {
    $html_content = file_get_contents($file);
    if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $html_content, $matches)) {
        $html_content = $matches[1];
    }
    echo $html_content;
} else {
    http_response_code(404);
    echo "Not found";
}
?>
