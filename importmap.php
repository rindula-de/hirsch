<?php

/*
 * (c) Sven Nolting, 2023
 */

return [
    'app' => [
        'path' => 'app.js',
        'preload' => true,
    ],
    'jquery' => [
        'downloaded_to' => 'vendor/jquery.js',
        'url' => 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/+esm',
        'preload' => true,
    ],
    '@hotwired/stimulus' => [
        'url' => 'https://cdn.jsdelivr.net/npm/@hotwired/stimulus@3.2.2/+esm',
    ],
    '@hotwired/turbo' => [
        'url' => 'https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.0-beta.1/+esm',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
];
