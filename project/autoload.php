<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base = __DIR__ . '/src/';
    
    if (strpos($class, $prefix) !== 0) return;
    
    $relative = substr($class, strlen($prefix));
    $file = $base . str_replace('\\', '/', $relative) . '.php';
    
    if (file_exists($file)) require $file;
});