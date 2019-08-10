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


function arrayDeepSet(&$array, $value, ...$keys) {
    if (isset($keys[4])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]])) {
            $array[$keys[0]][$keys[1]][$keys[2]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]])) {
            $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]] = $value;
        return;
    }
    if (isset($keys[3])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]])) {
            $array[$keys[0]][$keys[1]][$keys[2]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
        return;
    }
    if (isset($keys[2])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]] = $value;
        return;
    }
    if (isset($keys[1])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        $array[$keys[0]][$keys[1]] = $value;
        return;
    }
    if (isset($keys[0])) {
        $array[$keys[0]] = $value;
    }
}

