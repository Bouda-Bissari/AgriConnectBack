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



    // public function register(RegisterRequest $request)
    // {
    //     $data = $request->validated();


    //     /** @var User $user */

    //     // Ajouter le code pays 228 si ce n'est pas déjà présent
    //     $phoneNumber = $request->phone_number;
    //     $phoneNumber = '228' . $phoneNumber;


    //     $user = User::create([
    //         'phone_number' => $data['phone_number'],
    //         'fullName' => $data['fullName'],
    //         'password' => bcrypt($data['password']),
    //     ]);

    //     $token = $user->createToken('main')->plainTextToken;

    //     // Assigner le rôle à l'utilisateur
    // $role = Role::where('name', $data['role'])->first();

    // if (!$role) {
    //     return response()->json(['error' => 'Invalid role'], 400);
    // }

    // // Associer le rôle à l'utilisateur
    // UserRole::updateOrCreate(
    //     ['user_id' => $user->id],
    //     ['role_id' => $role->id]
    // );

    //     // Envoyer l'OTP
    // // Créez une instance de Request avec le numéro de téléphone
    // $otpRequest = new Request([
    //     'phone_number' => $phoneNumber,
    // ]);

    // // Créez une instance de SignController
    // $otpController = new SignController();

    // // Appelez la méthode sendOtp en passant l'objet Request
    // $otpController->sendOtp($otpRequest);
    //     return response()->json([
    //         'message' => 'Utilisateur créé avec succès. Un OTP a été envoyé à votre numéro de téléphone.',
    //         'user' => $user,
    //         'token' => $token,
    //         'role' => $role,

    //     ]);

    //     // return response()->json([
    //     //     'user' => $user,
    //     //     'token' => $token,
    //     // ]);

    // }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Validation du numéro de téléphone
        $phoneNumber = $data['phone_number'];
        if (!preg_match('/^\d{8,}$/', $phoneNumber)) {
            return response()->json(['error' => 'Le numéro de téléphone est invalide.'], 400);
        }

        // Ajouter le code pays 228 si ce n'est pas déjà présent
        $phoneNumber = $phoneNumber[0] === '2' ? $phoneNumber : '228' . $phoneNumber;

        // Créez l'utilisateur
        $user = User::create([
            'phone_number' => $phoneNumber,
            'fullName' => $data['fullName'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        // Assigner le rôle à l'utilisateur
        $role = Role::where('name', $data['role'])->first();

        if (!$role) {
            return response()->json(['error' => 'Rôle invalide'], 400);
        }

        // Associer le rôle à l'utilisateur
        UserRole::updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $role->id]
        );

        // Envoyer l'OTP
        $otpRequest = new Request([
            'phone_number' => $phoneNumber,
        ]);

        $otpController = new SignController();
        $otpController->sendOtp($otpRequest);

        return response()->json([
            'message' => 'Utilisateur créé avec succès. Un OTP a été envoyé à votre numéro de téléphone.',
            'user' => $user,
            'token' => $token,
            'role'  => $role->name,
        ]);
    }







    // public function login(LoginRequest $request)
    // {
    //     $credentials = $request->validated();
    //     if (!Auth::attempt($credentials)) {
    //         return response(
    //             [
    //                 'message' => 'Mauvais numero ou mot de passe'
    //             ]
    //         );
    //     }
    //     /** @var User $user */
    //     $user = Auth::user();
    //     $token = $user->createToken('user')->plainTextToken;

    //     $role = $user->roles()->first()->name;

    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token,
    //         'role' => $role,
    //     ]);

    // }
    public function login(LoginRequest $request)
{
    $credentials = $request->validated();
       // Vérifier le mot de passe de l'utilisateur
       if (!Auth::attempt($credentials)) {
        // Si le mot de passe est incorrect, retourner une réponse spécifique
        return response()->json([
            'message' => 'Mot de passe incorrect',
        ], 401); // Code HTTP 401 Unauthorized
    }
    /** @var User $user */
    $user = Auth::user();
    $token = $user->createToken('user')->plainTextToken;

    // Récupérer tous les rôles de l'utilisateur et les formater en chaîne
    $roles = $user->roles->pluck('name');

    return response()->json([
        'user' => $user,
        'token' => $token,
        'roles' => $roles,
    ]);
}




    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return response('', 204);

    }
}
