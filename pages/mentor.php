<?php
$file = __DIR__ . '/mentor-profile.html';
if (file_exists($file)) {
    readfile($file);
} else {
    http_response_code(404);
    echo "Not found";
}
exit;
