<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class NotificationCandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer l'utilisateur authentifié
        $authenticatedUser = Auth::user();
        $user = User::find($authenticatedUser->id);

        // Assurez-vous que l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Récupérer les notifications de l'utilisateur authentifié
        $notifications = $user->notifications;
        return response()->json($notifications);
    }


//     public function index()
// {
//     // Récupérer toutes les notifications
//     $notifications = DatabaseNotification::all();

//     // Retourner les notifications sous forme de JSON
//     return response()->json($notifications);
// }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Méthode non utilisée
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Méthode non utilisée
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $authenticatedUser = Auth::user();

        if (!$authenticatedUser) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $notification = $authenticatedUser->notifications;

        if ($notification) {
            return response()->json($notification);
        }
        return response()->json(['error' => 'Notification non trouvée'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Méthode non utilisée
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $authenticatedUser = Auth::user();
        $user = User::find($authenticatedUser->id);

        if (!$authenticatedUser) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Rechercher la notification par UUID
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();  // Marquer la notification comme lue
            return response()->json(['message' => 'Notification marquée comme lue']);
        }

        return response()->json(['error' => 'Notification non trouvée'], 404);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $authenticatedUser = Auth::user();

        if (!$authenticatedUser) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }
        $user = User::find($authenticatedUser->id);

        // Rechercher la notification par UUID
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification supprimée']);
        }

        return response()->json(['error' => 'Notification non trouvée'], 404);
    }

}
