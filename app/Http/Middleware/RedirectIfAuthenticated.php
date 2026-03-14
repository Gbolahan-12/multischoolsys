<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectTo());
            }
        }

        return $next($request);
    }

    protected function redirectTo(): string
    {
        $user = Auth::user();

        if (! $user) {
            return route('login');
        }

        return match($user->role) {
            'super-admin' => route('superadmin.dashboard'),
            'proprietor'  => route('proprietor.dashboard'),
            'admin'       => route('admin.dashboard'),
            'staff'  => route('staff.dashboard'),
            'school-user' => route('staff.dashboard'),
            default       => route('login'),
        };
    }
}