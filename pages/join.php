<?php
$file = __DIR__ . '/join-program.html';
if (file_exists($file)) {
    readfile($file);
} else {
    http_response_code(404);
    echo "Not found";
}
exit;
