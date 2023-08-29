<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Models\Movie;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * /movies GET route.
     * Returns a list of movies paginated by 10, sorted by movie added datetime.
     * Optional param title to search for a movie by title.
     */
    public function index(Request $request): string
    {
        if ($request->has('title')) {
            $movies = Movie::where('title', 'LIKE', '%'.$request->input('title').'%')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $movies = Movie::orderBy('created_at', 'desc')->paginate(10);
        }

        return $movies->toJson();
    }

    /**
     * /top-upcoming-movies GET route.
     * Returns a list of top upcoming movies (Rating > 7.0) paginated by 10, sorted by closest upcoming broadcast.
     */
    public function topUpcoming(): string
    {
        $movies = Movie::where('released_at', '>', now())
            ->where('rating', '>', 7.0)
            ->join('broadcasts', function (JoinClause $join) {
                $join->on('movies.id', '=', 'broadcasts.movie_id')
                    ->where('broadcasts.broadcasted_at', '>=', now())
                    ->orderBy('broadcasts.broadcasted_at', 'desc')
                    ->take(1);
            })
            ->orderBy('broadcasts.broadcasted_at', 'desc')
            ->paginate(10);

        return $movies->toJson();
    }

    /**
     * /movies/create POST route.
     * Creates a new movie and returns it.
     */
    public function store(MovieRequest $request): string
    {
        if (! is_array($request->validated())) {
            return response()->json([
                'message' => 'Invalid request data',
            ], 400);
        }

        $movie = Movie::create($request->validated());

        return response()->json([
            'message' => 'Movie created succesfully !',
            'movie' => $movie->toJson(),
        ]);
    }

    /**
     * /movies/delete/{id} DELETE route.
     * Finds a movie by the ID, if it exists, deletes it and returns a success message.
     */
    public function destroy(string $id): string
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return response()->json([
            'message' => 'Movie deleted succesfully !',
        ]);
    }
}
