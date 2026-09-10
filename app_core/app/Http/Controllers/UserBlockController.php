<?php

namespace App\Http\Controllers;

use App\Models\UserBlock;
use Illuminate\Http\Request;

class UserBlockController extends Controller
{
    public function store(Request $request, $userId)
    {
        if ((int)$userId === auth()->id()) {
            return response()->json(['message' => 'You cannot block yourself.'], 422);
        }

        UserBlock::firstOrCreate([
            'blocker_id' => auth()->id(),
            'blocked_id' => $userId,
        ]);

        return response()->json(['message' => 'User blocked.', 'blocked' => true]);
    }

    public function destroy($userId)
    {
        UserBlock::where('blocker_id', auth()->id())
            ->where('blocked_id', $userId)
            ->delete();

        return response()->json(['message' => 'User unblocked.', 'blocked' => false]);
    }
}
