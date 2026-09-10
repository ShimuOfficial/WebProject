<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'logout',
        '/sslcommerz/success',
        '/success',
        '/sslcommerz/fail',
        '/sslcommerz/cancel',
        '/sslcommerz/ipn',
        '/sslcommerz/pay-via-ajax',
    ];
}
