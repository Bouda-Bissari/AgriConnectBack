<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function register(RegisterRequest $request)
    {
        $data = $request->validated();


        /** @var User $user */

        // Ajouter le code pays 228 si ce n'est pas déjà présent
        $phoneNumber = $request->phone_number;
        $phoneNumber = '228' . $phoneNumber;
    

        $user = User::create([
            'phone_number' => $data['phone_number'],
            'fullName' => $data['fullName'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        // Assigner le rôle à l'utilisateur
    $role = Role::where('name', $data['role'])->first();

    if (!$role) {
        return response()->json(['error' => 'Invalid role'], 400);
    }

    // Associer le rôle à l'utilisateur
    UserRole::updateOrCreate(
        ['user_id' => $user->id],
        ['role_id' => $role->id]
    );

        // Envoyer l'OTP
    // Créez une instance de Request avec le numéro de téléphone
    $otpRequest = new Request([
        'phone_number' => $phoneNumber,
    ]);

    // Créez une instance de SignController
    $otpController = new SignController();

    // Appelez la méthode sendOtp en passant l'objet Request
    $otpController->sendOtp($otpRequest);
        return response()->json([
            'message' => 'Utilisateur créé avec succès. Un OTP a été envoyé à votre numéro de téléphone.',
            'user' => $user,
            'token' => $token,
            'role' => $role,

        ]);

        // return response()->json([
        //     'user' => $user,
        //     'token' => $token,
        // ]);

    }



    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response(
                [
                    'message' => 'Mauvais numero ou mot de passe'
                ]
            );
        }
        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('user')->plainTextToken;

        $role = $user->roles()->first()->name;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'role' => $role,
        ]);

    }


    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return response('', 204);

    }
}
