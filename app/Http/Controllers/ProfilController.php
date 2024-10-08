<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
//use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePhoneRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Detail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $profiles = User::with('details','roles')->get();
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
        dd($request);

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



public function update(UpdateProfileRequest $request, $userId): JsonResponse
{


    // Récupérer l'utilisateur par son ID
    $user = User::with('details')->findOrFail($userId);

    // Mise à jour des informations de l'utilisateur
    $user->update($request->only(['fullName', 'phone_number']));

    // Mettre à jour le statut du profil
    $user->update(['is_completed' => true]);

    // Récupérer ou créer les détails associés
    $details = $user->details;

    if (!$details) {
        $details = new Detail();
        $details->user_id = $user->id;
    }

    // Mettre à jour les détails
    $details->fill($request->only([
        'email',
        'date',
        'gender',
        'bio',
        'company_name',
        'address',
        'domaine',
    ]));
    $details->save();

    // Gérer l'image
    if ($request->hasFile('image')) {
        // Enregistrer la nouvelle image
        $imagePath = $request->file('image')->store('images', 'public');
        $details->image = $imagePath;
        $details->save();  // Assurez-vous que les modifications sont enregistrées
    }

    // Les réponses (utile)
    return response()->json([
        'message' => 'Profil mis à jour avec succès',
        'details' => $details,
        'image' => $details->image,
        'completed' => $user->is_completed,
    ]);
}









// public function update(UpdateProfileRequest $request, $userId): JsonResponse
// {
//     $authenticatedUser = Auth::user();

//     // Vérifiez que l'utilisateur connecté peut mettre à jour ses propres informations
//     if ($authenticatedUser->id !== (int)$userId) {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $user = User::findOrFail($userId);

//     // Mise à jour des informations de l'utilisateur
//     $user->update($request->only(['fullName', 'phone_number']));

//     $details = $user->details;

//     if (!$details) {
//         $details = new Detail();
//         $details->user_id = $user->id;
//     }

//     $details->update($request->only([
//         'email',
//         'age',
//         'gender',
//         'bio',
//         'company_name',
//         'address',
//         'domaine'
//     ]));

//     if ($request->hasFile('image')) {
//         $file = $request->file('image');
//         $filePath = $file->store('avatars', 'public');
//         $user->image = $filePath;
//         $user->save();
//     }

//     return response()->json([
//         'message' => 'Profil mis à jour avec succès',
//         "details" => $details,
//         "request" => $request->all()
//     ]);
// }



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
