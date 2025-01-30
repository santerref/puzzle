<?php

require 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    if (strpos($class, 'Puzzle\\') !== 0) {
        return;
    }

    $parts = explode('\\', $class);
    if (count($parts) < 3) {
        return;
    }

    if (!preg_match('/^[a-z_]+$/', $parts[1])) {
        return;
    }

    $module = $parts[1];
    $relativePath = implode(DIRECTORY_SEPARATOR, array_slice($parts, 2)) . '.php';

    $filePaths = [
        __DIR__ . '/core/modules/' . $module . '/src/' . $relativePath,
        __DIR__ . '/modules/custom/' . $module . '/src/' . $relativePath,
        __DIR__ . '/modules/contrib/' . $module . '/src/' . $relativePath,
    ];

    foreach ($filePaths as $filePath) {
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
});
