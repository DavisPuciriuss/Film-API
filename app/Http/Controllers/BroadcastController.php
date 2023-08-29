<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Http\Requests\BroadcastRequest;

class BroadcastController extends Controller
{
    /**
     * /broadcasts/create POST request.
     * Creates a new broadcast if the movie doesn't already have a broadcast for this channel at this date.
     * Returns the broadcast if it was created, or the existing broadcast if it already exists.
     *
     * @param  BroadcastRequest $request
     * @return string
     */
    public function store(BroadcastRequest $request): string
    {
        // Check if the movie already has a broadcast for this channel at this date.
        $broadcast = Broadcast::where('movie_id', $request->validated()['movie_id'])
            ->where('broadcasted_at', $request->validated()['broadcasted_at']->format('Y-m-d') . '%')
            ->where('channel', $request->validated()['channel'])
            ->first();

        if($broadcast) {
            return response()->json([
                'message' => 'Broadcast already exists on this date for this channel !',
                'broadcast' => $broadcast->toJson(),
            ]);
        }

        $broadcast = Broadcast::create($request->validated());

        return response()->json([
            'message' => 'Broadcast created succesfully !',
            'broadcast' => $broadcast->toJson(),
        ]);
    }
}