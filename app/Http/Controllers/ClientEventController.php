<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $planners = \App\Models\User::where('role_id', 2)
                ->with(['events.reviews'])
                ->get()
                ->map(function ($planner) {
                    $reviews = $planner->events->flatMap->reviews;
                    $average_rating = $reviews->avg('rating');
                    $planner->average_rating = $average_rating ?? 0;
                    return $planner;
                })
                ->sortByDesc('average_rating')
                ->values();

            return response()->json(['message' => 'Top rated planners retrieved successfully', 'planners' => $planners]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve top rated planners', 'error' => $e->getMessage()], 500);
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
            'event_id' => 'required|exists:events,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        try {
            $review = new \App\Models\Review();
            $review->event_id = $validated['event_id'];
            $review->user_id = $request->user()->id;
            $review->rating = $validated['rating'];
            $review->comment = $validated['comment'];
            $review->save();

            return response()->json(['message' => 'Review submitted successfully', 'review' => $review], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to submit review', 'error' => $e->getMessage()], 500);
        }
    }
}
