<?php
// config/cache.php
require_once __DIR__ . '/config.php';

function cache_get($key) {
    if (APP_ENV === 'development') return false;
    
    $cache_file = __DIR__ . '/../cache/' . md5($key) . '.json';
    if (!file_exists($cache_file)) return false;
    
    $data = json_decode(file_get_contents($cache_file), true);
    if ($data['expires_at'] < time()) {
        @unlink($cache_file);
        return false;
    }
    return $data['content'];
}

function cache_set($key, $content, $ttl = 300) { // Default 5 mins
    if (APP_ENV === 'development') return;
    
    $cache_dir = __DIR__ . '/../cache/';
    if (!is_dir($cache_dir)) mkdir($cache_dir, 0777, true);
    
    $cache_file = $cache_dir . md5($key) . '.json';
    $data = [
        'expires_at' => time() + $ttl,
        'content' => $content
    ];
    file_put_contents($cache_file, json_encode($data));
}
