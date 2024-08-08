<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePhoneRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Detail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $profiles = User::with('details')->get();
        return response()->json(($profiles));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProfileRequest $request
     * @return JsonResponse
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'phone_number' => $validated['phone_number'],
            'fullName' => $validated['fullName'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->details()->create($validated['details']);

        return response()->json([
            'message' => 'Profile Created',
            'profile' => new ($user->load('details'))
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show($userId): JsonResponse
{
    $user = User::with('details')->find($userId);
    $authenticatedUser = Auth::user();

    // // Vérifiez que l'utilisateur connecté demande ses propres détails
    // if (!$user || $authenticatedUser->id !== (int)$userId) {
    //     return response()->json(['error' => 'Unauthorized'], 403);
    // }

    return response()->json($user);
}

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProfileRequest $request
     * @param User $user
     * @return JsonResponse
     */
//     public function update(UpdateProfileRequest $request, $userId)
//     {

//         //dump($request);
//         // Récupérer l'utilisateur par son ID
//         $user = User::findOrFail($userId);
    
//         // Mise à jour des informations de l'utilisateur
//         $user->update($request->only(['fullName', 'phone_number']));
    
//         // Récupérer ou créer les détails associés
//         $details = $user->details;
    
//         if (!$details) {
//             // Si aucun détail n'existe, en créer un nouveau
//             $details = new Detail();
//             $details->user_id = $user->id;
//         }
    
//         // Mise à jour des détails
//         $details->update($request->only([
//             'email',
//             'age',
//             'gender',
//             'bio',
//             'company_name',
//             'address',
//             'domaine'
//         ]));
    
//         // Gérer la mise à jour de l'image si nécessaire
//         if ($request->hasFile('avatar_url')) {
//             $file = $request->file('avatar_url');
//             $filePath = $file->store('avatars', 'public');
//             $user->avatar_url = $filePath;
//             $user->save();
//         }
    
//         return response()->json(['message' => 'Profil mis à jour avec succès',
//     "details"=>$details,
// "request" => $request->all()]);
//     }

public function update(UpdateProfileRequest $request, $userId): JsonResponse
{
    $authenticatedUser = Auth::user();

    // Vérifiez que l'utilisateur connecté peut mettre à jour ses propres informations
    if ($authenticatedUser->id !== (int)$userId) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $user = User::findOrFail($userId);

    // Mise à jour des informations de l'utilisateur
    $user->update($request->only(['fullName', 'phone_number']));

    $details = $user->details;

    if (!$details) {
        $details = new Detail();
        $details->user_id = $user->id;
    }

    $details->update($request->only([
        'email',
        'age',
        'gender',
        'bio',
        'company_name',
        'address',
        'domaine'
    ]));

    if ($request->hasFile('avatar_url')) {
        $file = $request->file('avatar_url');
        $filePath = $file->store('avatars', 'public');
        $user->avatar_url = $filePath;
        $user->save();
    }

    return response()->json([
        'message' => 'Profil mis à jour avec succès',
        "details" => $details,
        "request" => $request->all()
    ]);
}
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->details()->delete();
        $user->delete(); 

        return response()->json(['message' => 'Profile Deleted']);
    }

    /**
     * Update the phone number of the specified user.
     *
     * @param UpdatePhoneRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function updatePhoneNumber(UpdatePhoneRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        $user->update([
            'phone_number' => $validated['phone_number']
        ]);

        return response()->json([
            'message' => 'Numéro de téléphone mis à jour avec succès.'
        ]);
    }

    /**
     * Delete the profile image of the authenticated user.
     *
     * @return JsonResponse
     */
    public function deleteProfileImage(): JsonResponse
    {
        $user = Auth::user();

        if ($user && $user->profile_image && Storage::exists($user->profile_image)) {
            Storage::delete($user->profile_image);

            $user->profile_image = null;

            return response()->json(['message' => 'Image de profil supprimée avec succès.']);
        }

        return response()->json(['message' => 'Aucune image de profil à supprimer.'], 404);
    }
}
