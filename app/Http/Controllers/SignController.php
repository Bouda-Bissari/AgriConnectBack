<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Assurez-vous d'importer le Log
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use App\Models\User;
use Illuminate\Contracts\Session\Session;

class SignController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        $phoneNumber = $request->input('phone_number');

        $otp = rand(1000, 9999);

        // Enregistrer l'OTP dans la session
        // $request->session()->put('otp', $otp);
        // session(['otp' => $otp]);

        // Si vous utilisez un service de SMS, décommentez et configurez
        /*
        $basic = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $client = new Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($phoneNumber, env('VONAGE_FROM'), 'Votre code OTP est: ' . $otp)
        );
        */

        return response()->json(['message' => 'Code OTP envoyé.', 'otp' => $otp]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        // Find the user by phone number
        $user = User::where('phone_number', $request->phone_number)->first();

        if ($user) {
            // Update the is_blocked status to false
            $user->is_blocked = false;
            $user->save();

            return response()->json(['message' => 'Compte activé avec succès.']);
        } else {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }
    }
}




