<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * DEFENSE: §5.2 / §5.3 — guests on /customer/* go to customer login, others to /login
 */
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('customer') || $request->is('customer/*')) {
            return route('customer.login');
        }

        return route('login');
    }
}
