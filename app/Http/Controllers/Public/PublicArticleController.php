<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->published()
            ->with(['category', 'destination', 'creator'])
            ->when($request->filled('category'), fn (Builder $query) => $query->where('article_category_id', $request->integer('category')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.articles.index', [
            'articles' => $articles,
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'destination', 'creator'])
            ->firstOrFail();

        return view('public.articles.show', [
            'article' => $article,
        ]);
    }
}
