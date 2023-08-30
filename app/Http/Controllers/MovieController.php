<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Models\Movie;
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
        $movies = Movie::upcoming()
            ->ratingAbove(7.0)
            ->with('broadcasts')
            ->orderByClosestBroadcast()
            ->paginate(10);

        return $movies->toJson();
    }

    /**
     * /movies/create POST route.
     * Creates a new movie and returns it.
     *
     * @return array<string,int|string>
     */
    public function store(MovieRequest $request): array
    {
        if (! is_array($request->validated())) {
            return [
                'message' => 'Invalid request data',
                'status' => 400,
            ];
        }

        $movie = Movie::create($request->validated());

        return [
            'message' => 'Movie created succesfully !',
            'status' => 201,
            'movie' => $movie->toJson(),
        ];
    }

    /**
     * /movies/delete/{id} DELETE route.
     * Finds a movie by the ID, if it exists, deletes it and returns a success message.
     *
     * @return array<string,int|string>
     */
    public function destroy(string $id): array
    {
        $movie = Movie::find($id);

        if (! $movie) {
            return [
                'message' => 'Movie not found !',
                'status' => 404,
            ];
        }

        $movie->delete();

        return [
            'message' => 'Movie deleted succesfully !',
            'status' => 200,
        ];
    }
}
