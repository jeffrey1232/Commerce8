<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login-multi-role');
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return redirect('/login-multi-role')->with('error', 'Votre compte a été désactivé');
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($user->role, $roles)) {
            // Rediriger selon le rôle de l'utilisateur
            return $this->redirectToDashboard($user);
        }

        return $next($request);
    }

    private function redirectToDashboard($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'vendor':
                return redirect('/vendor/dashboard');
            case 'client':
                return redirect('/client/validation');
            case 'community_manager':
                return redirect('/studio/dashboard');
            case 'staff':
                return redirect('/admin/dashboard');
            default:
                return redirect('/login-multi-role');
        }
    }
}
