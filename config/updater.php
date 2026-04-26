<?php

return [
    // Comma-separated list in .env: UPDATE_ALLOWED_HOSTS=license.v***.com,viser***.com
    'allowed_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('UPDATE_ALLOWED_HOSTS', 'license.viserlab.com,viserlab.com'))))),

    // Require HTTPS for update URLs.
    'require_https' => (bool) env('UPDATE_REQUIRE_HTTPS', true),
];
