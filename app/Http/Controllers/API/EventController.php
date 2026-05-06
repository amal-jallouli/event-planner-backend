<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['category', 'creator'])->withCount('registrations');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('place', 'like', "%{$request->search}%");
            });
        }

        if ($request->category_id)  { $query->where('category_id', $request->category_id); }
        if ($request->status)        { $query->where('status', $request->status); }
        if ($request->date_from)     { $query->where('start_date', '>=', $request->date_from); }
        if ($request->date_to)       { $query->where('start_date', '<=', $request->date_to); }
        if ($request->has('is_free')) { $query->where('is_free', $request->boolean('is_free')); }

        $events = $query->orderBy('start_date', 'asc')
                        ->paginate($request->per_page ?? 9);

        $events->getCollection()->transform(function ($event) {
            $event->available_spots = $event->capacity - $event->registrations_count;
            return $event;
        });

        return response()->json($events);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        if ($data['is_free']) { $data['price'] = 0; }

        $event = Event::create($data);
        $event->load(['category', 'creator']);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        $event->load(['category', 'creator']);
        $event->loadCount('registrations');
        $event->available_spots = $event->capacity - $event->registrations_count;
        return response()->json($event);
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($event->image) { Storage::disk('public')->delete($event->image); }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        if (isset($data['is_free']) && $data['is_free']) { $data['price'] = 0; }

        $event->update($data);
        $event->load(['category', 'creator']);
        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        if ($event->image) { Storage::disk('public')->delete($event->image); }
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
