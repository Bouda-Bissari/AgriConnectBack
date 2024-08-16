<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    public function index()
{
    // Récupère toutes les candidatures avec leurs relations
    $candidatures = Candidature::with('user', 'service','service.user')->get();

    return response()->json($candidatures);
}

    public function store(Request $request)
    {
        // Valide la requête
        $request->validate([
            // 'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        // Crée la candidature
        $candidature = Candidature::create([
            'user_id' => Auth::id(),
            'service_id' => $request->input('service_id'),
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Candidature créée avec succès',
            'candidature' => $candidature
        ], 201);
    }

    public function show($id)
    {
        // Récupère la candidature

        $candidature = Candidature::with('service','user','service.user')->findOrFail($id);
        // $candidature = Candidature::with('user', 'service','service.user')->get();

        return response()->json($candidature);
    }



//recuperer les candiadture grace a l'id du postulant

public function getCandidaturesByUser($userId)
{
    $candidatures = Candidature::with('service', 'user', 'service.user')
                    ->where('user_id', $userId)
                    ->get();

    return response()->json($candidatures);
}

//Recuperer grace au service id


public function getCandidaturesByService($serviceId)
{
    $candidatures = Candidature::with('service', 'user', 'service.user')
                    ->where('service_id', $serviceId)
                    ->get();

    return response()->json($candidatures);
}




//Pour récupérer les candidatures en fonction de l'ID de l'utilisateur qui a posté le service 

public function getCandidaturesByServiceOwner($serviceOwnerId)
{
    // Récupère tous les services postés par l'utilisateur avec $serviceOwnerId
    $services = Service::where('user_id', $serviceOwnerId)->pluck('id');

    // Récupère toutes les candidatures associées à ces services
    $candidatures = Candidature::with('service', 'user', 'service.user.details','user.details')
                    ->whereIn('service_id', $services)
                    ->get();

    return response()->json($candidatures);
}


    public function update(Request $request, $id)
    {
        // Valide la requête
        $request->validate([
            'message' => 'nullable|string',
        ]);

        // Récupère et met à jour la candidature
        $candidature = Candidature::findOrFail($id);
        // $this->authorize('update', $candidature);

        $candidature->update([
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Candidature mise à jour avec succès',
            'candidature' => $candidature
        ]);
    }

    public function destroy($id)
    {
        // Récupère et supprime la candidature
        $candidature = Candidature::findOrFail($id);
        // $this->authorize('delete', $candidature);
        $candidature->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }
}
