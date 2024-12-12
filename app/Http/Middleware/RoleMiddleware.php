<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Vérifier si l'utilisateur est authentifié et s'il a le rôle requis
        if ($user && $user->roles->where('name', $role)->exists()) {
            return $next($request);
        }

        // Obtenir le token de la requête
        $token = $request->bearerToken();

        // Obtenir les rôles de l'utilisateur pour la réponse
        $userRoles = $user ? $user->roles->pluck('name')->toArray() : [];

        // Si l'utilisateur n'est pas authentifié ou n'a pas le rôle requis
        return response()->json([
            'message' => 'Unauthorized.',
            'required_role' => $role,
            'user_roles' => $userRoles,
            'auth_status' => $user ? 'Authenticated' : 'Not Authenticated',
            'token' => $token
        ], 403);
    }
}
