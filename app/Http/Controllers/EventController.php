<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
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
        $events = Event::where('planner_id', $user->id)->orderBy('created_at', 'desc')->get();
        $reviews = Review::where('planner_id', $user->id)->get();
        return response()->json([
            'events' => $events,
            'reviews' => $reviews
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'client_id' => 'nullable|exists:clients,id',
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
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'event_date' => 'required|date',
                'category' => 'required|string',
                'price' => 'required|numeric',
                'status' => 'required|in:active,completed',
            ]);

            $id->update($validated);

            return response()->json([
            'message' => 'Event updated successfully',
            'event' => $id
            ]);
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
     * Remove the specified resource from storage.
     */
    public function destroy(Event $id)
    {
        $id->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
    public function getBookingRequests()
    {
        $requests = BookingRequest::where('planner_id', Auth::id())->with('planner','user')->get();

        return response()->json($requests);
    }
    public function updateBookingRequestStatusAccept(Request $request, $id)
    {

        $bookingRequest = BookingRequest::findOrFail($id);

        if ($bookingRequest->planner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $event = Event::create([
            'title' => $bookingRequest->title,
            'client_id' => $bookingRequest->user_id,
            'planner_id' => $bookingRequest->planner_id,
            'description' => $bookingRequest->description,
            'event_date' => $bookingRequest->event_date,
            'category' => $bookingRequest->category,
            'price' => $bookingRequest->price,
            'status' => 'active',
        ]);
        $bookingRequest->delete();

        return response()->json([
            'message' => 'Booking request status updated successfully',
            'bookingRequest' => $bookingRequest
        ]);
    }
    public function updateBookingRequestStatusDecline(Request $request, $id)
    {

        $bookingRequest = BookingRequest::findOrFail($id);

        if ($bookingRequest->planner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookingRequest->delete();

        return response()->json([
            'message' => 'Booking request deleted successfully',
            'bookingRequest' => $bookingRequest
        ]);
    }
    public function editPlannerInfo(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->planner) {
                return response()->json([
                    'message' => 'No planner profile found for this user'
                ], 404);
            }
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'bio' => 'sometimes|nullable|string',
            ]);

            $user->planner->update($validated);

            return response()->json([
                'message' => 'Planner information updated successfully',
                'planner' => $user->planner
            ]);
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
}