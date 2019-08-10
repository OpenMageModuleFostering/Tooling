<?php

namespace OpenMageModuleFostering\Tooling;


/**
 * @param string $path
 * @return mixed
 */
function getConfig($path) {
    $config = require __DIR__ . '/../config.php';
    return $config[$path];
}

function fetch_file_with_cache($url, $throttle = 0) {
    $escapedUrl = preg_replace('/[^A-Za-z0-9_\-]/', '_', $url);
    $parsedUrl = parse_url($url);
    $escapedDomain = preg_replace('/[^A-Za-z0-9_\-]/', '_', $parsedUrl['host']);

    $cachedPath = getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain . '/' . $escapedUrl;

    if (file_exists($cachedPath)) {
        return file_get_contents($cachedPath);
    }

    if ($throttle > 0) {
        sleep($throttle);
    }
    $content = file_get_contents($url);
    if (!file_exists(getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain)) {
        mkdir(getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain,0777,true);
    }
    file_put_contents($cachedPath, $content);

    return $content;

}

