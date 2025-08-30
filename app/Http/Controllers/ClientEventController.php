<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientEventController extends Controller
{
    public function index()
    {
        try {
            $planners = \App\Models\User::where('role_id', 2)->get();
            return response()->json(['message' => 'Planners retrieved successfully', 'planners' => $planners]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve planners', 'error' => $e->getMessage()], 500);
        }
    }

        public function plannerSearch(Request $request)
    {
        try {
            $query = $request->input('q');

            // Validate query
            if (empty($query)) {
            return response()->json(['error' => 'Query is required'], 400);
            }

            $planners = \App\Models\User::where('role_id', 2)
            ->where('name', 'like', '%' . $query . '%')
            ->get();

            return response()->json($planners);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to search planners', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTopRatedAllPlanners(Request $request)
    {
        try {
            $planners = \App\Models\User::where('role_id', 1)
                ->with([
                    'reviews' => function($query) {
                        $query->with('user:id,name')->orderBy('created_at', 'desc');
                    },
                    'planner'
                ])
                ->get()
                ->map(function ($planner) {
                    // Calculate average rating from direct reviews
                    $average_rating = $planner->reviews->avg('rating');
                    $planner->average_rating = round($average_rating ?? 0, 1);
                    $planner->disabled = (Auth::user()->bookingRequests()->where('planner_id', $planner->id)->exists());
                    return $planner;
                })
                ->sortByDesc('average_rating')
                ->values();

            return response()->json([
                'message' => 'Top rated planners retrieved successfully',
                'planners' => $planners
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve top rated planners',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getPlanner($id)
    {
        try {
            $planner = \App\Models\User::where('role_id', 1)->findOrFail($id);
            return response()->json(['message' => 'Planner retrieved successfully', 'planner' => $planner]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve planner', 'error' => $e->getMessage()], 500);
        }
    }
    public function leaveReview(Request $request){
        $validated = $request->validate([
            'planner_id' => 'required|exists:users,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        try {
            $review = new \App\Models\Review();
            $review->planner_id = $validated['planner_id'];
            $review->user_id = $request->user()->id;
            $review->rating = $validated['rating'];
            $review->comment = $validated['comment'];
            $review->save();

            return response()->json(['message' => 'Review submitted successfully', 'review' => $review], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit review', 'error' => $e->getMessage()], 500);
        }
    }
    public function addRequest(Request $request){
        $validated = $request->validate([
            'planner_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
        ]);
        try {
            $bookingRequest = new \App\Models\BookingRequest();
            $bookingRequest->planner_id = $validated['planner_id'];
            $bookingRequest->user_id = Auth::user()->id;
            $bookingRequest->title = $validated['title'];
            $bookingRequest->description = $validated['description'];
            $bookingRequest->event_date = $validated['event_date'];
            $bookingRequest->category = $validated['category'];
            $bookingRequest->price = $validated['price'];
            $bookingRequest->status = 'pending';
            $bookingRequest->save();

            return response()->json(['message' => 'Booking request submitted successfully', 'bookingRequest' => $bookingRequest], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit booking request', 'error' => $e->getMessage()], 500);}
    }

    public function getClients()
    {
        try {
            $clients = \App\Models\User::where('role_id', 2)->get();
            return response()->json(['message' => 'Clients retrieved successfully', 'clients' => $clients]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve clients', 'error' => $e->getMessage()], 500);
        }
    }
}
