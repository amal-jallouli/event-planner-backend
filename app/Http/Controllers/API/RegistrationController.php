<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    // POST /registrations/{event}
    public function store(Request $request, Event $event)
    {
        $user = $request->user();

        if ($user->registeredEvents()->where('event_id', $event->id)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 422);
        }

        if ($event->status !== 'actif') {
            return response()->json(['message' => "Cet événement n'est pas actif."], 422);
        }

        $registered = $event->registrations()->count();
        if ($registered >= $event->capacity) {
            return response()->json(['message' => 'Plus de places disponibles.'], 422);
        }

        $registration = Registration::create([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        return response()->json([
            'message'         => 'Inscription réussie.',
            'registration'    => $registration,
            'available_spots' => $event->capacity - ($registered + 1),
        ], 201);
    }

    // DELETE /registrations/{event}
    public function destroy(Request $request, Event $event)
    {
        $registration = Registration::where('user_id', $request->user()->id)
            ->where('event_id', $event->id)->first();

        if (!$registration) {
            return response()->json(['message' => "Vous n'êtes pas inscrit à cet événement."], 404);
        }

        $registration->delete();
        return response()->json(['message' => 'Désinscription réussie.']);
    }

    // GET /registrations/my
    public function myRegistrations(Request $request)
    {
        $registrations = Registration::with(['event.category'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($registrations);
    }

    // GET /registrations (admin)
    public function index(Request $request)
    {
        $registrations = Registration::with(['user', 'event.category'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($registrations);
    }

    // GET /registrations/check/{event}
    public function check(Request $request, Event $event)
    {
        $isRegistered = $request->user()
            ->registeredEvents()->where('event_id', $event->id)->exists();

        return response()->json(['is_registered' => $isRegistered]);
    }
}