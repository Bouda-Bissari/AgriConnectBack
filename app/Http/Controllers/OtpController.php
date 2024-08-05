<?
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use App\Models\User;

class OtpController extends Controller
{
    public function sendOtp($phoneNumber)
    {
        $basic = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $client = new Client($basic);

        $otp = rand(100000, 999999);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($phoneNumber, env('VONAGE_FROM'), 'Votre code OTP est: ' . $otp)
        );

        // Enregistrer l'OTP dans la session ou dans la base de données
        session(['otp' => $otp]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'otp' => 'required',
        ]);

        $inputOtp = $request->otp;
        $sessionOtp = session('otp');

        if ($inputOtp == $sessionOtp) {
            return response()->json(['message' => 'OTP vérifié avec succès.']);
        } else {
            return response()->json(['message' => 'OTP invalide.'], 422);
        }
    }
}
