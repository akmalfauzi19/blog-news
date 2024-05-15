<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('query');
        $category = $request->input('category');

        $query = Article::with(['category'])
            ->where('status', 'publish');

        if ($category && $category !== 'all') {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $data = $query->when(isset($search), function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->paginate(5);

        $categories = Category::get();

        return view('page.users.index', compact('data', 'categories'));
    }

    public function show($slug): View
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $categories = Category::get();
        return view('page.users.show', compact('article', 'categories'));
    }
}
