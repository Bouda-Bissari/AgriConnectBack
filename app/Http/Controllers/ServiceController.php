<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Fetch all services with optional filtering, sorting, etc.
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed for API, typically used for forms in traditional apps
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        // Vérifiez si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated']);
        }
    
        // Validez et stockez le nouveau service
        $validated = $request->validated();
    
        // Créez le service avec les données validées
        $service = new Service($validated);
    
        // Associez le service à l'utilisateur authentifié
        $service->user_id = Auth::id(); // L'ID de l'utilisateur authentifié
            // Gestion du fichier image
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $service->image = $imagePath;
    }

        $service->save();
    
        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Service $service): JsonResponse
    {
        // Return the specific service
        return response()->json($service);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        // Not needed for API, typically used for forms in traditional apps
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        // Validate and update the service
        $validated = $request->validated();

        // Update the service with the validated data
        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service
        ]);
    }


    public function userServices($userId): JsonResponse
    {

        $authenticatedUser = Auth::user();

        // Vérifiez que l'utilisateur connecté demande ses propres services
        if ($authenticatedUser->id !== (int)$userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // Fetch the user by ID
        $user = User::findOrFail($userId);

        // Get all services associated with the user
        $services = $user->services; 
        return response()->json($services);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        // Delete the service
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
