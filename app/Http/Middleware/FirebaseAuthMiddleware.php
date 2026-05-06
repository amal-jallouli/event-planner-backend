<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseAuthMiddleware
{
    // Google's public keys endpoint for Firebase
    private const GOOGLE_KEYS_URL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
    private const PROJECT_ID = 'eventplanner-91e0b';

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token manquant.'], 401);
        }

        try {
            $decoded = $this->verifyFirebaseToken($token);

            $uid   = $decoded->sub ?? null;
            $email = $decoded->email ?? null;

            if (!$uid || !$email) {
                return response()->json(['message' => 'Token invalide.'], 401);
            }

            // Find user in DB
            $user = User::where('firebase_uid', $uid)->first()
                 ?? User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'Utilisateur introuvable.'], 401);
            }

            // Save firebase_uid if missing
            if (!$user->firebase_uid) {
                $user->update(['firebase_uid' => $uid]);
            }

            Auth::setUser($user);
            $request->setUserResolver(fn() => $user);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Token invalide.',
                'error'   => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }

    private function verifyFirebaseToken(string $token): object
    {
        // Fetch Google public keys (with cache)
        $cacheFile = storage_path('app/firebase_keys_cache.json');
        $keys = null;

        // Use cache if less than 1 hour old
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

        // Try each key until one works
        $lastError = null;
        foreach ($keys as $keyId => $certificate) {
            try {
                $decoded = JWT::decode($token, new Key($certificate, 'RS256'));

                // Validate Firebase-specific claims
                if ($decoded->aud !== self::PROJECT_ID) {
                    throw new \Exception('Audience invalide.');
                }
                if ($decoded->iss !== 'https://securetoken.google.com/' . self::PROJECT_ID) {
                    throw new \Exception('Issuer invalide.');
                }
                if ($decoded->exp < time()) {
                    throw new \Exception('Token expiré.');
                }

                return $decoded;
            } catch (\Throwable $e) {
                $lastError = $e;
                continue;
            }
        }

        throw new \Exception('Vérification du token échouée: ' . ($lastError?->getMessage() ?? 'Erreur inconnue'));
    }
}