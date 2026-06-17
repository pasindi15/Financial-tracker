<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateExport
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        $plainToken = $request->query('token');

        if ($plainToken) {
            $accessToken = PersonalAccessToken::findToken($plainToken);

            if ($accessToken?->tokenable) {
                Auth::login($accessToken->tokenable);

                return $next($request);
            }
        }

        return redirect('/login')->with('error', 'Please sign in again to download reports.');
    }
}
