<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Candidature;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\CandidatureNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(): JsonResponse
    // {
    //     $services = Service::where('deleted', false)->get();
    //     return response()->json($services);
    // }


    public function index(): JsonResponse
{
    $services = Service::where('deleted', false)
                        ->orderByRaw("CASE WHEN deadline >= NOW() THEN 0 ELSE 1 END")
                        ->orderBy('updated_at', 'desc')
                        ->orderBy('deadline', 'desc')
                        ->get();

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
    // public function store(StoreServiceRequest $request): JsonResponse
    // {
    //     // Vérifiez si l'utilisateur est authentifié
    //     if (!Auth::check()) {
    //         return response()->json(['error' => 'User not authenticated']);
    //     }

    //     // Validez et stockez le nouveau service
    //     $validated = $request->validated();

    //     // Créez le service avec les données validées
    //     $service = new Service($validated);

    //     // Associez le service à l'utilisateur authentifié
    //     $service->user_id = Auth::id();
    //         // Gestion du fichier image
    // if ($request->hasFile('image')) {
    //     $imagePath = $request->file('image')->store('images', 'public');
    //     $service->image = $imagePath;
    // }

    //     $service->save();

    //     return response()->json([
    //         'message' => 'Service created successfully',
    //         'service' => $service
    //     ], 201);
    // }



//     public function store(StoreServiceRequest $request): JsonResponse
// {
//     // Vérifiez si l'utilisateur est authentifié
//     if (!Auth::check()) {
//         return response()->json(['error' => 'Utilisateur non authentifié']);
//     }

//     // Validez et stockez le nouveau service
//     $validated = $request->validated();

//     // Créez le service avec les données validées
//     $service = new Service($validated);

//     // Associez le service à l'utilisateur authentifié
//     $service->user_id = Auth::id();

//     // Gestion du fichier image
//     if ($request->hasFile('image')) {
//         $imagePath = $request->file('image')->store('images', 'public');
//         $service->image = $imagePath;
//     }

//     // Sauvegardez le service dans la base de données
//     $service->save();

//     return response()->json(['message' => 'Service créé avec succès', 'service' => $service]);
// }


public function store(StoreServiceRequest $request): JsonResponse
{
    // Vérifiez si l'utilisateur est authentifié
    if (!Auth::check()) {
        return response()->json(['error' => 'Utilisateur non authentifié']);
    }

     /** @var User $user */
    $user = Auth::user();

    // Vérifiez si l'utilisateur a déjà le rôle "exploitant"
    $roles = $user->roles()->pluck('name');

    if (!$roles->contains('exploitant')) {
        $exploitantRole = Role::where('name', 'exploitant')->first();
        if ($exploitantRole) {
            // Ajoutez le rôle "exploitant" à l'utilisateur
            UserRole::updateOrCreate(
                ['user_id' => $user->id, 'role_id' => $exploitantRole->id]
            );
        }
    }

    // Validez et stockez le nouveau service
    $validated = $request->validated();

    // Créez le service avec les données validées
    $service = new Service($validated);

    // Associez le service à l'utilisateur authentifié
    $service->user_id = $user->id;

    // Gestion du fichier image
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $service->image = $imagePath;
    }

    // Sauvegardez le service dans la base de données
    $service->save();



    return response()->json(['message' => 'Service créé avec succès',
     'service' => $service,
    'roles'=> $roles]);
}




    /**
     * Display the specified resource.
     */
    public function show(Service $service): JsonResponse
    {
        // Vérifiez si le service est marqué comme supprimé
        if ($service->deleted) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Retournez le service s'il n'est pas supprimé
        return response()->json($service);
    }


    /**
 * Update the 'deleted' status of the specified resource.
 */
public function updateDeletedStatus(Request $request, Service $service): JsonResponse
{
    // Vérifiez si l'utilisateur est authentifié
    if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Valider que la valeur de 'deleted' est un booléen
    $request->validate([
        'deleted' => 'required|boolean',
    ]);

    // Mettre à jour la colonne 'deleted'
    $service->deleted = $request->input('deleted');
    $service->save();


    if ($service->deleted) {
        // Mettre à jour le statut des candidatures associées à 'deleted'
        $candidatures = Candidature::where('service_id', $service->id)->get();
        foreach ($candidatures as $candidature) {
            $candidature->status = 'deleted';
            $candidature->save();

            // Informer les postulants que le service a été supprimé
            $applyingUserDetails = [
                'body' => 'Le service pour lequel vous avez postulé a été supprimé: ' . $service->title,
                'url' => url('/candidatures/' . $candidature->id)
            ];
            $candidature->user->notify(new CandidatureNotification($applyingUserDetails));
        }
    }



    return response()->json([
        'message' => 'Service deleted status updated successfully',
        'service' => $service
    ]);
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
        // Vérifiez si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Validation des données
        $validated = $request->validated();

        // Mettez à jour les champs valides
        $service->update($validated);

        // Gestion du fichier image
        if ($request->hasFile('image')) {
            // Supprimez l'ancienne image si elle existe
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            // Stockez la nouvelle image
            $imagePath = $request->file('image')->store('images', 'public');
            $service->image = $imagePath;
            $service->save();
        }

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

        // Get all services associated with the user and filter out the deleted ones
        $services = $user->services()->where('deleted', false)->get();

        return response()->json($services);
    }


    //recuperer le nombre associer a une candidature

    public function countApplications($serviceId): JsonResponse
    {
        // Valider que l'ID du service est un entier et existe dans la base de données
        $validated = Validator::make(
            ['service_id' => $serviceId],
            ['service_id' => 'required|integer|exists:services,id']
        );

        if ($validated->fails()) {
            return response()->json(['error' => 'Invalid service ID'], 400);
        }

        // Trouvez le service par ID
        $service = Service::findOrFail($serviceId);


                // Vérifiez si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Obtenez le nombre de candidatures associées au service
        $count = $service->candidatures()->count();

        return response()->json([
            'service_id' => $service->id,
            'applications_count' => $count
        ]);
    }


//recupere les candidatures associes
public function getApplications($serviceId): JsonResponse
{
    // Valider que l'ID du service est un entier et existe dans la base de données
    $validated = Validator::make(
        ['service_id' => $serviceId],
        ['service_id' => 'required|integer|exists:services,id']
    );

    if ($validated->fails()) {
        return response()->json(['error' => 'Invalid service ID'], 400);
    }

    // Trouvez le service par ID
    $service = Service::findOrFail($serviceId);

    // Vérifiez si l'utilisateur est authentifié
    if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Obtenez les candidatures associées au service
    $applications = $service->candidatures;

    return response()->json([
        'service_id' => $service->id,
        'applications' => $applications
    ]);
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
