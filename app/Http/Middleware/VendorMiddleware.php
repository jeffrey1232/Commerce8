<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
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

        // Vérifier si l'utilisateur est vendeur
        if (!$user->isVendor()) {
            return redirect('/login-multi-role')->with('error', 'Accès réservé aux vendeurs');
        }

        // Vérifier si le profil vendeur existe
        if (!$user->vendor) {
            return redirect('/vendor/setup')->with('info', 'Veuillez compléter votre profil vendeur');
        }

        // Vérifier si le compte vendeur est actif
        if ($user->vendor->status !== 'active') {
            return redirect('/login-multi-role')->with('error', 'Votre compte vendeur a été désactivé');
        }

        return $next($request);
    }
}
