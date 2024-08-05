<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdatePhoneRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pas besoin de cette méthode si vous ne l'utilisez pas pour les vues
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Création de l'utilisateur
        $user = User::create([
            'phone_number' => $validated['phone_number'],
            'password' => bcrypt($validated['password']),
        ]);

        // Attachement des rôles, s'ils sont présents
        if (isset($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }

        return response()->json(['message' => 'User Created'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user->load('roles'); // Charge les rôles associés

        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Pas besoin de cette méthode si vous ne l'utilisez pas pour les vues
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        // Mise à jour des attributs de l'utilisateur
        $user->update([
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $user->password,
        ]);

        // Mise à jour des rôles, s'ils sont présents
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return response()->json(['message' => 'User Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->roles()->detach(); // Détache les rôles associés
        $user->delete();

        return response()->json(['message' => 'User Deleted']);
    }

    public function updatePhoneNumber(UpdatePhoneRequest $request, User $user)
    {
        $validated = $request->validated();

        // Mise à jour du numéro de téléphone
        $user->update([
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
        ]);

        // Envoyer l'OTP
        // $otpRequest = new Request([
        //     'phone_number' => $user->phone_number,
        // ]);

        // $otpController = new SignController();
        // $otpController->sendOtp($otpRequest);

        return response()->json([
            'message' => 'Numéro de téléphone mis à jour avec succès. Un OTP a été envoyé à votre numéro de téléphone.',
        ]);
    }
    public function updateUserRole(Request $request): JsonResponse
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['error' => 'Invalid role'], 400);
        }

        // Mettre à jour le rôle de l'utilisateur
        UserRole::updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $role->id]
        );

        return response()->json(['message' => 'Role updated successfully'], 200);
    }

    // public function updateUserRole(Request $request,User $user,Role $role,UserRole $userRole): JsonResponse
    // {
    //     $request->validate([
    //         'role' => 'required|string|exists:roles,name',
    //     ]);


    //      $role = Role::where('name', $request->role)->first();
 
    //      if (!$role) {
    //          return response()->json(['error' => 'Invalid role'], 400);
    //      }
 
    //             // Mettre à jour le rôle de l'utilisateur
    //             UserRole::updateOrCreate(
    //                 ['user_id' => $user->id],
    //                 ['role_id' => $role->id]
    //             );
 
    //      return response()->json(['message' => 'Role updated successfully'], 200);
    // }

}

