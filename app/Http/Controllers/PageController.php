<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use App\Support\SiteContent;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(SiteContent $site): View
    {
        return view('home', [
            'heroSlides' => HeroSlide::where('is_published', true)->whereNotNull('image')->orderBy('sort_order')->orderBy('id')->get(),
            'packages' => Package::upcoming()->orderBy('departure_date')->limit(3)->get(),
            'minimumPrice' => Package::upcoming()->min('price'),
            'articles' => Article::published()->latest('published_at')->limit(3)->get(),
            'testimonials' => Testimonial::where('is_published', true)->where('consent_confirmed', true)->latest()->limit(3)->get(),
            'title' => $site->text('home_title', config('seo.home_title')),
            'description' => $site->text('home_description', config('seo.home_description')),
        ]);
    }

    public function show(string $page): View
    {
        $record = Page::where('slug', $page)->first();
        if ($record) {
            abort_unless($record->is_published, 404);

            return view($page === 'kontak' ? 'pages.kontak' : 'pages.managed', [
                'pageRecord' => $record, 'title' => $record->seo_title ?: $record->title,
                'description' => $record->seo_description ?: Str::limit($record->intro ?? $record->title, 160),
                'localPackages' => $page === 'umrah-jogja' ? Package::upcoming()->orderBy('departure_date')->limit(3)->get() : collect(),
            ]);
        }
        $pages = [
            'tentang-kami' => ['Tentang Safir Caturjaya Wisata', 'Mengenal PT Safir Caturjaya Wisata, layanan umrah, dan informasi izin PPIU berdasarkan dokumen perusahaan.'],
            'umrah-jogja' => ['Travel Umrah Jogja, Bantul & seluruh Indonesia', 'Layanan konsultasi umrah Safir untuk Yogyakarta, Bantul, Sleman, Kulon Progo dan Gunungkidul. Temukan alamat kantor dan persiapan perjalanan.'],
            'kontak' => ['Hubungi Safir Jogja', 'Konsultasi paket umrah dengan Wisnu melalui WhatsApp 0821-3604-2015. Temukan kantor Bantul dan konsultasi online.'],
            'panduan-umrah' => ['Panduan Persiapan Umrah', 'Langkah memilih paket, memahami biaya, menyiapkan dokumen, dan merencanakan umrah dari Yogyakarta maupun daerah lain.'],
            'privasi' => ['Kebijakan Privasi', 'Informasi penggunaan data dan layanan pihak ketiga pada situs Safir Caturjaya Wisata Jogja.'],
        ];
        abort_unless(isset($pages[$page]), 404);

        return view('pages.'.$page, ['title' => $pages[$page][0], 'description' => $pages[$page][1]]);
    }
}
