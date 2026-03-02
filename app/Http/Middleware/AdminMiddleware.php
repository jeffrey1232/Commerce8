<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login-multi-role');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur est admin ou staff
        if (!$user->isAdmin() && !$user->isStaff()) {
            return redirect('/login-multi-role')->with('error', 'Accès non autorisé');
        }

        // Vérifier si le compte est actif
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect('/login-multi-role')->with('error', 'Votre compte a été désactivé');
        }

        return $next($request);
    }
}
