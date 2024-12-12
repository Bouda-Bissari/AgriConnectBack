<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{


    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants incorrects',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->roles()->where('name', 'admin')->exists()) {
            return response()->json([
                'message' => 'Accès non autorisé',
            ], 403);
        }

        $token = $user->createToken('user')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'role' => 'admin',
        ]);
    }



    // Récupère tous les utilisateurs avec leurs services et détails associés
    public function index()
    {
        $users = User::with('services', 'details')->get();
        return response()->json($users);
    }

    // Récupère tous les utilisateurs ayant le rôle 'exploitant' avec leurs services et détails associés
    public function getExploitants()
    {
        // Récupère le rôle 'exploitant'
        $exploitantRole = Role::where('name', 'exploitant')->first();
        if (!$exploitantRole) {
            return response()->json(['error' => 'Rôle exploitant non trouvé'], 404);
        }

        // Récupère les utilisateurs ayant le rôle 'exploitant'
        $users = User::with('services', 'details')
            ->whereHas('roles', function ($query) use ($exploitantRole) {
                $query->where('role_id', $exploitantRole->id);
            })
            ->get();

        return response()->json($users);
    }

    // Récupère tous les utilisateurs ayant le rôle 'ouvrier' avec leurs détails associés
    public function getOuvriers()
    {
        // Récupère le rôle 'ouvrier'
        $ouvrierRole = Role::where('name', 'ouvrier')->first();
        if (!$ouvrierRole) {
            return response()->json(['error' => 'Rôle ouvrier non trouvé'], 404);
        }

        // Récupère les utilisateurs ayant le rôle 'ouvrier'
        $users = User::with('details')
            ->whereHas('roles', function ($query) use ($ouvrierRole) {
                $query->where('role_id', $ouvrierRole->id);
            })
            ->get();

        return response()->json($users);
    }

    // Récupère un utilisateur spécifique avec ses services et détails associés
    public function show($id)
    {
        $user = User::with('services', 'details')->findOrFail($id);
        return response()->json($user);
    }

    // Récupère les utilisateurs ayant un rôle spécifique avec leurs services et détails associés
    public function getUsersByRole($roleName)
    {
        // Récupère le rôle par son nom
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return response()->json(['error' => 'Rôle non trouvé'], 404);
        }
    
        // Récupère les utilisateurs ayant le rôle spécifié et les trie par date de création
        $users = User::with('details')
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('role_id', $role->id);
            })
            ->orderBy('created_at', 'desc') // Tri par date de création, du plus récent au plus ancien
            ->get();
    
        return response()->json($users);
    }
    



    //compter les users
    public function countUsers(): JsonResponse
    {
          $count = User::count();
          return response()->json(['count' => $count]);
        // return response()->json("That'sme");
    }



    // compter par role

    public function countUsersByRole(Request $request)
    {
        $roleName = $request->query('role');
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return response()->json(['count' => 0]);
        }

        $count = User::whereHas('roles', function ($query) use ($role) {
            $query->where('role_id', $role->id);
        })->count();

        return response()->json(['count' => $count]);
    }
    //compter les services


    public function countServices()
    {
        $count = Service::count(); // Compte le nombre total de services
        return response()->json(['count' => $count]);
    }
}
