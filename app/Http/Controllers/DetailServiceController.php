<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class DetailServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Récupérer tous les services avec les utilisateurs associés
        $services = Service::with('user')->get();
        return response()->json($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données de la requête
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|string|max:255',
            'deadline' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'image' => 'nullable|string', // Peut être un chemin de fichier ou une URL
        ]);

        // Création du nouveau service
        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service créé avec succès',
            'service' => $service
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Rechercher le service par ID avec l'utilisateur associé
        $service = Service::with('user')->find($id);
    
    // Récupérer l'ID de l'utilisateur associé au service
    $userId = $service->user_id;

    // Rechercher le profil de l'utilisateur associé au service
    $profiles = User::find($userId)->details;
    
        if (!$service) {
            return response()->json(['error' => 'Service non trouvé'], 404);
        }
    
        // Formater et retourner la réponse JSON
        return response()->json([
            'service' => $service,
            'profiles' => $profiles
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Rechercher le service par ID
        $service = Service::findOrFail($id);

        // Validation des données de la requête
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'sometimes|string|max:255',
            'deadline' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'image' => 'nullable|string', // Peut être un chemin de fichier ou une URL
        ]);

        // Mise à jour des informations du service
        $service->update($validated);

        return response()->json([
            'message' => 'Service mis à jour avec succès',
            'service' => $service
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // Rechercher le service par ID
        $service = Service::findOrFail($id);

        // Suppression du service
        $service->delete();

        return response()->json(['message' => 'Service supprimé avec succès']);
    }
}
