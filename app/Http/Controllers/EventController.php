<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $events = Event::with('planner')
            ->whereHas('planner', function ($query) use ($user) {
                $query->where('id', $user->id); // Correct column
            })
            ->get();

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:draft,published',
        ]);

        $validated['planner_id'] = Auth::user()->id;

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $id)
    {
        return response()->json($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'event_date' => 'sometimes|required|date',
            'category' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|in:draft,published',
        ]);

        $id->update($validated);

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $id)
    {
        $id->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}
