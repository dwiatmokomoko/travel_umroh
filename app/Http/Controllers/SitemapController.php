<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect(['home', 'packages.index', 'news.index', 'testimonials'])->map(fn ($name) => ['loc' => route($name), 'lastmod' => null]);
        foreach (['tentang-kami', 'umrah-jogja', 'kontak', 'panduan-umrah', 'privasi'] as $slug) {
            if (! Page::where('slug', $slug)->exists()) {
                $urls->push(['loc' => url('/'.$slug), 'lastmod' => null]);
            }
        }
        foreach (Page::where('is_published', true)->get(['slug', 'updated_at']) as $page) {
            $urls->push(['loc' => url('/'.$page->slug), 'lastmod' => $page->updated_at->toIso8601String()]);
        }
        foreach (Package::upcoming()->get(['slug', 'updated_at']) as $package) {
            $urls->push(['loc' => route('packages.show', $package->slug), 'lastmod' => $package->updated_at->toIso8601String()]);
        }
        foreach (Article::published()->get(['slug', 'updated_at']) as $article) {
            $urls->push(['loc' => route('news.show', $article->slug), 'lastmod' => $article->updated_at->toIso8601String()]);
        }

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
