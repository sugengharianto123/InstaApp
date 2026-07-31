<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request adalah API atau mengharapkan JSON, JANGAN redirect (return null)
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Return null untuk mencegah Laravel mencoba mencari route 'login'
        return null;
    }
}
