<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role === 'super-admin') {
            return $next($request);
        }

        $school = $user->school;

        if (!$school) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is not linked to a school.']);
        }

        if ($school->isPending()) {
            return redirect()->route('school.pending');
        }

        if ($school->isBanned()) {
            return redirect()->route('school.banned');
        }

        return $next($request);
    }
}