<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class EnsureQuizAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        
        if (!Session::has('quiz_auth_user_id') || Session::get('quiz_auth_slug') !== $slug) {
            return redirect()->route('qcm.login', $slug)->withErrors(['auth' => 'Veuillez vous identifier pour accéder à ce test.']);
        }

        return $next($request);
    }
}
