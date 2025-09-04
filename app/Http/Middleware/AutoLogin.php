<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $user = User::where('email', 'user@user.com')->first();
            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
