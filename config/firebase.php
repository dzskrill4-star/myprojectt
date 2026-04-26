<?php

return [
    // Absolute path to Firebase Admin SDK credentials JSON (service account).
    // Keep this OUT of any public web directory.
    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase/push_config.json')),

    // Optional: if you prefer to store the JSON as base64 in env.
    'credentials_json_base64' => env('FIREBASE_CREDENTIALS_JSON_BASE64'),
];
