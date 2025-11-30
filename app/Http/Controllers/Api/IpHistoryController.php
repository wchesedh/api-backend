<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IpHistoryController extends Controller
{
    /**
     * Get IP search history for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $history = DB::table('ip_search_history')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return response()->json($history);
    }

    /**
     * Store a new IP search in history
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'ip' => 'required|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'country' => 'nullable|string',
            'loc' => 'nullable|string',
        ]);

        // Check if this IP already exists for this user
        $existing = DB::table('ip_search_history')
            ->where('user_id', $user->id)
            ->where('ip', $request->ip)
            ->first();

        if ($existing) {
            // Update the data but DON'T change created_at to keep original order
            DB::table('ip_search_history')
                ->where('id', $existing->id)
                ->update([
                    'city' => $request->city ?? $existing->city,
                    'region' => $request->region ?? $existing->region,
                    'country' => $request->country ?? $existing->country,
                    'loc' => $request->loc ?? $existing->loc,
                    'updated_at' => now()
                ]);
            
            return response()->json(['message' => 'History updated', 'id' => $existing->id]);
        }

        // Insert new record
        $id = DB::table('ip_search_history')->insertGetId([
            'user_id' => $user->id,
            'ip' => $request->ip,
            'city' => $request->city ?? 'Unknown',
            'region' => $request->region ?? 'Unknown',
            'country' => $request->country ?? 'Unknown',
            'loc' => $request->loc,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['id' => $id, 'message' => 'History saved']);
    }

    /**
     * Delete selected IP history items
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $deleted = DB::table('ip_search_history')
            ->where('user_id', $user->id)
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json(['message' => "Deleted {$deleted} item(s)"]);
    }
}

