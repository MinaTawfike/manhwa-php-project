<?php

/**
 * Trusted proxy configuration.
 *
 * Set the `TRUSTED_PROXIES` environment variable to a single IP, a
 * comma-separated list of IPs/CIDRs, or `*` to trust all proxies.
 * Example: TRUSTED_PROXIES=10.0.0.1,10.0.0.2
 */

return [
    'proxies' => env('TRUSTED_PROXIES') ? array_map('trim', explode(',', env('TRUSTED_PROXIES'))) : null,
];
