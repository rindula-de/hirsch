<?php

/*
 * (c) Sven Nolting, 2023
 */

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
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
