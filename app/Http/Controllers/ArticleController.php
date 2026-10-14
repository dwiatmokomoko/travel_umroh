<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Testimonial;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request, SiteContent $site): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:150'], 'page' => ['nullable', 'integer', 'min:1']]);
        $articles = Article::published()->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q')->toString().'%'))->latest('published_at')->paginate(9)->withQueryString();

        return view('articles.index', ['articles' => $articles, 'title' => $site->text('news_title', config('seo.news_title')), 'description' => $site->text('news_description', config('seo.news_description')), 'canonical' => $request->integer('page', 1) > 1 ? route('news.index', ['page' => $request->integer('page')]) : route('news.index'), 'noindex' => $request->filled('q')]);
    }

    public function show(string $slug): View
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        return view('articles.show', ['article' => $article, 'title' => $article->seo_title ?: $article->title, 'description' => $article->seo_description ?: $article->excerpt, 'canonical' => route('news.show', $article->slug), 'ogType' => 'article', 'socialImage' => $article->image ? route('media', $article->image) : null]);
    }

    public function testimonials(Request $request, SiteContent $site): View
    {
        $request->validate(['page' => ['nullable', 'integer', 'min:1']]);

        return view('testimonials', ['testimonials' => Testimonial::where('is_published', true)->where('consent_confirmed', true)->latest()->paginate(12), 'title' => $site->text('testimonials_title', config('seo.testimonials_title')), 'description' => $site->text('testimonials_description', config('seo.testimonials_description')), 'canonical' => $request->integer('page', 1) > 1 ? route('testimonials', ['page' => $request->integer('page')]) : route('testimonials')]);
    }
}
