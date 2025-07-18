<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
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
        $events = Event::with(['planner', 'reviews.user']) // Eager load user inside reviews
            ->whereHas('planner', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->get();

        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $id)
    {
        return response()->json($id);
    }

    public function storeReview(Request $request)
    {
        try {
            $validated = $request->validate([
          'event_id' => 'required|exists:events,id',
          'rating' => 'required|integer|min:1|max:5',
          'comment' => 'nullable|string',
            ]);

            $validated['user_id'] = Auth::id();

            $review = Review::create($validated);

            return response()->json([
          'message' => 'Review submitted successfully.',
          'review' => $review
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
          'message' => 'Validation failed',
          'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
          'message' => 'Something went wrong',
          'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getReviews($id)
    {
        $reviews = Review::where('event_id', $id)->with('user')->get();

        return response()->json($reviews);
    }
    public function updateReview(Request $request, $id, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review
        ]);
    }
    public function deleteReview($id, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
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
