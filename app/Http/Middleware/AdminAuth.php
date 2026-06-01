<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        // Restriction d'accès à la Recherche Opérationnelle pour les simples observateurs
        if ($user->role !== 'super_admin' && $request->is('admin/recherche-operationnelle*')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les droits nécessaires pour accéder à ce module.');
        }

        return $next($request);
    }
}
