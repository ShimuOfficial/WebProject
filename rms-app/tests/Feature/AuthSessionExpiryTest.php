<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class AuthSessionExpiryTest extends TestCase
{
    public function test_expired_login_submission_redirects_back_to_login(): void
    {
        $request = request()->create('/login', 'POST');

        $response = $this->app
            ->make(ExceptionHandler::class)
            ->render($request, new TokenMismatchException());

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/login', $response->headers->get('Location'));
    }
}
