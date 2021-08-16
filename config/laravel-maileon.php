<?php

return [
    'api-url'       => env('MAILEON_API_URL', 'https://api.maileon.com/1.0'),
    'api-key'       => env('MAILEON_API_KEY'),
    'contact-event' => env('MAILEON_TRANSACTIONAL_CONTACT_EVENT'),

    // Specify the FQN to the PSR-18 compliant HTTP Client you want to use.
    // If none is explicitly provided, the package will try to find a PSR-18
    // compliant HTTP Client using "php-http/discovery" and use that.
    'http-client'   => null,

    // Specify the FQN to the PSR-3 compliant logger you wish to use. If none
    // is provided, it will use whatever logger is bound to the
    // Psr\Log\LoggerInterface. If that resolve into null, all
    'logger'        => null,
    'log-requests'  => env('MAILEON_LOG_REQUESTS', env('APP_DEBUG', false)),
];
