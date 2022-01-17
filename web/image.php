<?php

$file = __DIR__ . getenv('REQUEST_URI');
if (!is_file($file)) {
    exit;
}

$webp = $file . '.webp';
if (!is_file($webp)) {
    exec('/usr/bin/convert ' . $file . ' ' . $webp, $output, $res);
}
header('Content-type:image/webp');
readfile($webp);
