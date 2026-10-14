<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{ asset('carousel.js') }}?v=1" defer></script>
    <link rel="stylesheet" href="{{ asset('chat.css') }}?v=1">
    <script src="{{ asset('chat.js') }}?v=1" defer></script>
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if(config('services.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('services.google_site_verification') }}">
    @endif
    <meta name="robots" content="{{ config('safir.indexable') && !($noindex ?? false) ? 'index,follow,max-image-preview:large' : 'noindex,follow' }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta name="theme-color" content="#492538">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('safir.name') }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:image" content="{{ $socialImage ?? $site->image('hero_image', 'images/madinah.jpg') }}">
    <meta property="og:image:alt" content="{{ $socialImageAlt ?? $title }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $socialImage ?? $site->image('hero_image', 'images/madinah.jpg') }}">
    <meta name="twitter:image:alt" content="{{ $socialImageAlt ?? $title }}">
    @if(isset($article))
        <meta name="author" content="{{ $article->author }}">
        <meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
        <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
    @endif
    <link rel="icon" href="{{ $site->image('logo_image', 'images/safir-logo.jpeg') }}">
    <link rel="stylesheet" href="{{ asset('site.css') }}?v=4">
    <script src="{{ asset('site.js') }}?v=4" defer></script>
    <link rel="stylesheet" href="{{ asset('media.css') }}?v=1">

    @php
        $business = [
            '@context' => 'https://schema.org', '@type' => 'TravelAgency', '@id' => route('home').'#business',
            'name' => config('safir.name'), 'legalName' => config('safir.company'), 'url' => route('home'),
            'telephone' => '+'.config('safir.whatsapp'), 'logo' => $site->image('logo_image', 'images/safir-logo.jpeg'),
            'contactPoint' => collect($site->consultants())->where('available', true)->map(fn ($contact) => ['@type' => 'ContactPoint', 'name' => $contact['name'], 'telephone' => '+'.$contact['phone'], 'contactType' => 'customer service', 'availableLanguage' => 'Indonesian', 'areaServed' => 'ID'])->values()->all(),
            'image' => $site->image('hero_image', 'images/madinah.jpg'), 'areaServed' => ['Indonesia', 'Daerah Istimewa Yogyakarta'],
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => config('safir.address'), 'addressLocality' => 'Bantul', 'addressRegion' => 'Daerah Istimewa Yogyakarta', 'addressCountry' => 'ID'],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($business, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @stack('head')
</head>
<body>
<a class="skip-link" href="#main">{{ $site->text('copy2_001') }}</a>
<div class="topbar"><div class="container"><span>{{ $site->text('copy2_002') }}</span>@if($site->pageVisible('umrah-jogja'))<a href="{{ route('local') }}">{{ $site->text('copy2_003') }}<span aria-hidden="true">↗</span></a>@endif</div></div>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="Safir Jogja — Beranda"><img src="{{ $site->image('logo_image', 'images/safir-logo.jpeg') }}" width="150" height="68" alt="{{ $site->text('access_004') }}"></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="navigation" hidden>{{ $site->text('copy2_004') }}<span aria-hidden="true">☰</span></button>
        <nav id="navigation" aria-label="Navigasi utama">
            @foreach(['home' => 'Beranda', 'packages.index' => 'Paket Umrah', 'about' => 'Tentang Safir', 'guide' => 'Panduan', 'news.index' => 'Berita', 'testimonials' => 'Testimoni', 'contact' => 'Kontak'] as $route => $label)
                @continue(!$site->navigationVisible($route))
                <a href="{{ route($route) }}" @if(request()->routeIs($route)) aria-current="page" @endif>{{ $site->text('nav_'.str_replace('.', '_', $route), $label) }}</a>
            @endforeach
        </nav>
        <a class="button small header-cta" href="{{ route('contact') }}#konsultan" target="_blank" rel="noopener noreferrer">{{ $site->text('copy2_005') }}<span aria-hidden="true">↗</span></a>
    </div>
</header>
<main id="main">@yield('content')@yield('community')
@if(request()->routeIs('privacy'))
<section class="container section"><h2>Peta dan video tersemat</h2><p>Halaman lokasi memuat peta Google Maps. Berita tertentu dapat memuat video YouTube. Saat konten tersemat dimuat, browser menghubungi penyedia tersebut yang memproses data teknis sesuai kebijakan privasinya.</p><h2>Percakapan asisten virtual</h2><p>Dalam mode AI, pertanyaan dan paling banyak enam pesan terakhir dikirim ke OpenAI untuk menyiapkan jawaban. Aplikasi tidak menyimpan isi percakapan ke database; riwayat di layar hanya berada di memori halaman sampai halaman dimuat ulang atau tombol hapus ditekan. Pemrosesan penyedia mengikuti kebijakan datanya. Mode informasi dasar tidak mengirim pesan ke OpenAI. Jangan masukkan data paspor, kesehatan, atau pembayaran. Tombol WhatsApp membuka percakapan dengan admin pilihan Anda; pesan dikirim setelah Anda mengirimkannya di WhatsApp.</p></section>
@endif
@if(request()->routeIs('home', 'contact', 'local')) @include('components.office-map') @endif
</main>
<section class="closing"><div class="container closing-inner"><div><p class="eyebrow">{{ $site->text('copy2_006') }}</p><h2>{{ $site->text('copy2_007') }}</h2><p>{{ $site->text('copy2_008') }}</p></div>@if($site->pageVisible('kontak'))<a class="button light" href="{{ route('contact') }}">{{ $site->text('copy2_009') }}<span aria-hidden="true">↗</span></a>@endif</div></section>
<footer><div class="container footer-grid"><div><div class="footer-brand">{{ $site->text('copy2_010') }}<span>{{ $site->text('copy2_011') }}</span></div><p>{{ $site->text('copy2_012') }}<br>{{ $site->text('copy2_013') }}</p><p class="fine">{{ $site->text('copy2_014') }}<br>Nomor izin PPIU {{ config('safir.license') }}</p></div><div><h3>{{ $site->text('copy2_015') }}</h3><a href="{{ route('packages.index') }}">{{ $site->text('copy2_016') }}</a>@if($site->pageVisible('umrah-jogja'))<a href="{{ route('local') }}">{{ $site->text('copy2_017') }}</a>@endif
@if($site->pageVisible('tentang-kami'))<a href="{{ route('about') }}">{{ $site->text('copy2_018') }}</a>@endif
@if($site->pageVisible('panduan-umrah'))<a href="{{ route('guide') }}">{{ $site->text('copy2_019') }}</a>@endif</div><div><h3>{{ $site->text('copy2_020') }}</h3><p>{{ config('safir.address') }}</p><a href="{{ config('safir.maps') }}" target="_blank" rel="noopener noreferrer">{{ $site->text('copy2_021') }}</a></div><div><h3>{{ $site->text('copy2_022') }}</h3><a href="tel:+{{ config('safir.whatsapp') }}">{{ config('safir.phone') }}</a><p>{{ config('safir.contact') }} · Konsultan perjalanan</p>@if($site->pageVisible('kontak'))<a href="{{ route('contact') }}">{{ $site->text('copy2_023') }}</a>@endif</div></div><div class="container extra-pages">@foreach($site->pages()->where('is_published', true) as $extraPage)@unless(in_array($extraPage->slug, ['tentang-kami','umrah-jogja','kontak','panduan-umrah','privasi']))<a class="text-link" href="{{ route('pages.show', $extraPage->slug) }}">{{ $extraPage->title }}</a>@endunless @endforeach</div><div class="container footer-bottom"><span>© {{ date('Y') }} Safir Caturjaya Wisata Jogja.</span>@if($site->pageVisible('privasi'))<a href="{{ route('privacy') }}">{{ $site->text('copy2_024') }}</a>@endif</div></footer>
@if($site->text('chatbot_enabled', '1') === '1')
    @include('components.chat-widget')
@endif
</body>
</html>
