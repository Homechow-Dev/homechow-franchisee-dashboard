<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        "https://orca-app-hoihb.ondigitalocean.app/*",
        "http://localhost:3000/*",
        
    ];
}
