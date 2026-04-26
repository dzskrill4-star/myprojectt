<?php

return [
    // Keep secrets in .env only. These values are read via config(), not env() in routes.
    'cron' => env('CRON_TOKEN', env('INTERNAL_CRON_TOKEN', '')),
    'clear' => env('CLEAR_TOKEN', env('INTERNAL_CLEAR_TOKEN', '')),
];
