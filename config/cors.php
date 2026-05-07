<?php

return [
    'paths'               => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'     => ['*'],
    'allowed_origins' => [
    'http://localhost:4200',
    'https://event-planner-front.vercel.app',
    'https://event-planner-front-9jyfz8qnt-amal-jalloulis-projects.vercel.app',
    'https://*.vercel.app',
],
    'allowed_origins_patterns' => [],
    'allowed_headers'     => ['*'],
    'exposed_headers'     => [],
    'max_age'             => 0,
    'supports_credentials' => false,
];
