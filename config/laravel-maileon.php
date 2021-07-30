<?php

return [
    'api-url'       => env('MAILEON_API_URL', 'https://api.maileon.com/1.0'),
    'api-key'       => env('MAILEON_API_KEY'),
    'contact-event' => env('MAILEON_TRANSACTIONAL_CONTACT_EVENT'),

    // Specify the FQN to the PSR-18 compliant HTTP Client you want to use.
    // If none is explicitly provided, the package will try to find a PSR-18
    // compliant HTTP Client using "php-http/discovery" and use that.
    'http-client'   => null,
];
