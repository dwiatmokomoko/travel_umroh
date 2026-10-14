@extends('layouts.site')
@push('head')
@php
    $testimonialsSchema = [
        '@context' => 'https://schema.org', '@type' => 'CollectionPage',
        '@id' => $canonical.'#webpage', 'url' => $canonical,
        'name' => $title, 'description' => $description, 'inLanguage' => 'id-ID',
        'about' => ['@id' => route('home').'#business'],
        'breadcrumb' => [
            '@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Testimoni Jamaah', 'item' => route('testimonials')],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($testimonialsSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@if($testimonials->previousPageUrl())<link rel="prev" href="{{ $testimonials->previousPageUrl() }}">@endif
@if($testimonials->nextPageUrl())<link rel="next" href="{{ $testimonials->nextPageUrl() }}">@endif
@endpush
@section('content')<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy14_001') }}</p><h1>{{ $site->text('copy14_002') }}</h1><p>{{ $site->text('copy14_003') }}</p></section><section class="container section"><div class="cards">@forelse($testimonials as $testimonial)<x-testimonial-card :testimonial="$testimonial" />@empty<div class="empty-state"><h2>{{ $site->text('copy14_004') }}</h2><p>{{ $site->text('copy14_005') }}</p></div>@endforelse</div>@include('partials.pagination', ['paginator' => $testimonials])</section>@endsection
