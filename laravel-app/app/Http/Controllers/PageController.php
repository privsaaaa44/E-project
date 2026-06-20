<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function home(): View
    {
        $featuredMovies = DB::table('movies as m')
            ->leftJoin('shows as s', 's.movie_id', '=', 'm.id')
            ->leftJoin('bookings as b', 'b.show_id', '=', 's.id')
            ->leftJoin('movie_category as mc', 'mc.movi_id', '=', 'm.id')
            ->leftJoin('category as c', 'c.id', '=', 'mc.cat_id')
            ->selectRaw('m.id, m.title, m.poster, m.rating, COUNT(b.id) as booking_count, GROUP_CONCAT(c.category_name SEPARATOR ", ") as categories')
            ->where('m.movie_status', 'now_showing')
            ->groupBy('m.id', 'm.title', 'm.poster', 'm.rating')
            ->orderByDesc('booking_count')
            ->orderByDesc('m.rating')
            ->limit(4)
            ->get();

        $movies = DB::table('movies as m')
            ->leftJoin('movie_category as mc', 'mc.movi_id', '=', 'm.id')
            ->leftJoin('category as c', 'c.id', '=', 'mc.cat_id')
            ->selectRaw('m.id, m.title, m.duration, m.release_date, m.poster, m.movie_status, m.rating, GROUP_CONCAT(c.category_name SEPARATOR ", ") as categories')
            ->where('m.movie_status', '!=', 'archived')
            ->groupBy('m.id', 'm.title', 'm.duration', 'm.release_date', 'm.poster', 'm.movie_status', 'm.rating')
            ->orderByDesc('m.release_date')
            ->limit(12)
            ->get();

        return view('pages.home', compact('featuredMovies', 'movies'));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function movies(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $query = DB::table('movies as m')
            ->leftJoin('movie_category as mc', 'mc.movi_id', '=', 'm.id')
            ->leftJoin('category as c', 'c.id', '=', 'mc.cat_id')
            ->selectRaw('m.*, GROUP_CONCAT(c.category_name SEPARATOR ", ") as categories')
            ->where('m.movie_status', '!=', 'archived')
            ->groupBy(
                'm.id',
                'm.poster',
                'm.title',
                'm.trailer_link',
                'm.movie_desc',
                'm.duration',
                'm.created_at',
                'm.release_date',
                'm.director',
                'm.rating',
                'm.language',
                'm.movie_status',
                'm.is_featured',
                'm.genre'
            );

        if ($search !== '') {
            $query->havingRaw('m.title LIKE ? OR m.director LIKE ? OR categories LIKE ?', [
                "%{$search}%",
                "%{$search}%",
                "%{$search}%",
            ]);
        }

        $total = DB::query()->fromSub(clone $query, 'movie_results')->count();
        $movies = $query
            ->orderByDesc('m.release_date')
            ->forPage($page, $perPage)
            ->get();

        $paginator = new LengthAwarePaginator(
            $movies,
            $total,
            $perPage,
            $page,
            ['path' => route('movies.index'), 'query' => $request->query()]
        );

        return view('pages.movies', [
            'movies' => $paginator,
            'search' => $search,
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function bookings(): View
    {
        return view('pages.bookings');
    }
}
