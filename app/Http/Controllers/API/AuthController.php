<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    private const GOOGLE_KEYS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
    private const PROJECT_ID = 'eventplanner-91e0b';

    private function verifyFirebaseToken(string $token): object
    {
        $cacheFile = storage_path('app/firebase_keys_cache.json');
        $keys = null;

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
            $keys = json_decode(file_get_contents($cacheFile), true);
        }

        if (!$keys) {
            $response = file_get_contents(self::GOOGLE_KEYS_URL);
            if (!$response) {
                throw new \Exception('Impossible de récupérer les clés Google.');
            }
            $keys = json_decode($response, true);
            file_put_contents($cacheFile, $response);
        }

        $lastError = null;
        foreach ($keys as $certificate) {
            try {
                $decoded = JWT::decode($token, new Key($certificate, 'RS256'));

                if ($decoded->aud !== self::PROJECT_ID) {
                    throw new \Exception('Audience invalide.');
                }
                if ($decoded->iss !== 'https://securetoken.google.com/' . self::PROJECT_ID) {
                    throw new \Exception('Issuer invalide.');
                }

                return $decoded;
            } catch (\Throwable $e) {
                $lastError = $e;
                continue;
            }
        }

        throw new \Exception('Token invalide: ' . ($lastError?->getMessage() ?? 'Erreur inconnue'));
    }

    // Login via Firebase token
    public function firebaseLogin(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        try {
            $decoded = $this->verifyFirebaseToken($request->token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token Firebase invalide: ' . $e->getMessage()], 401);
        }

        $uid   = $decoded->sub;
        $email = $decoded->email;

        $user = User::where('firebase_uid', $uid)->first()
             ?? User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Compte introuvable. Veuillez vous inscrire.'], 404);
        }

        if (!$user->firebase_uid) {
            $user->update(['firebase_uid' => $uid]);
        }

        return response()->json(['user' => $user]);
    }

    // Register via Firebase token
    public function firebaseRegister(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'name'  => 'required|string|min:3',
            'email' => 'required|email',
        ]);

        try {
            $decoded = $this->verifyFirebaseToken($request->token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token Firebase invalide: ' . $e->getMessage()], 401);
        }

        $uid = $decoded->sub;

        // User already exists → update firebase_uid and return
        $existing = User::where('email', $request->email)->first();
        if ($existing) {
            if (!$existing->firebase_uid) {
                $existing->update(['firebase_uid' => $uid]);
            }
            return response()->json(['user' => $existing]);
        }

        // Create new user
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make(uniqid()),
            'role'         => 'user',
            'firebase_uid' => $uid,
        ]);

        return response()->json(['user' => $user], 201);
    }

    // Logout
    public function logout(Request $request)
    {
        return response()->json(['message' => 'Déconnecté avec succès.']);
    }
}