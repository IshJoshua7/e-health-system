<?php
// Test bootstrap: load Composer autoload if available, then include helpers.
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../lib/auth.php';
