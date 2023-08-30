<?php

namespace App\Http\Controllers;

use App\Http\Requests\BroadcastRequest;
use App\Models\Broadcast;

class BroadcastController extends Controller
{
    /**
     * /broadcasts/create POST request.
     * Creates a new broadcast if the movie doesn't already have a broadcast for this channel at this date.
     * Returns the broadcast if it was created, or the existing broadcast if it already exists.
     *
     * @return array<string,int|string>
     */
    public function store(BroadcastRequest $request): array
    {
        if (! is_array($request->validated()) || ! isset($request->validated()['movie_id']) || ! isset($request->validated()['broadcasted_at']) || ! isset($request->validated()['channel'])) {
            return [
                'message' => 'Invalid request data',
                'status' => 400,
            ];
        }

        $broadcasted_day = date_create_from_format('Y-m-d H:i:s', $request->validated()['broadcasted_at']);

        if ($broadcasted_day) {
            $broadcasted_day = $broadcasted_day->format('Y-m-d');
        } else {
            return [
                'message' => 'Invalid broadcasted_at date format',
                'status' => 400,
            ];
        }

        // Check if the movie already has a broadcast for this channel at this date.
        $broadcast = Broadcast::where('movie_id', $request->validated()['movie_id'])
            ->whereDate('broadcasted_at', $broadcasted_day)
            ->where('channel', $request->validated()['channel'])
            ->first();

        if ($broadcast) {
            return [
                'message' => 'Broadcast already exists on this date for this channel !',
                'status' => 409,
                'broadcast' => $broadcast->toJson(),
            ];
        }

        $broadcast = Broadcast::create($request->validated());

        return [
            'message' => 'Broadcast created succesfully !',
            'status' => 201,
            'broadcast' => $broadcast->toJson(),
        ];
    }
}
