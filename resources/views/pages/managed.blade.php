@extends('layouts.site')
@push('head')
@if($pageRecord->slug === 'umrah-jogja')
@php
    $localService = [
        '@context' => 'https://schema.org', '@type' => 'Service',
        '@id' => route('local').'#service', 'url' => route('local'),
        'name' => 'Layanan konsultasi perjalanan umrah Safir Jogja',
        'serviceType' => 'Konsultasi paket umrah',
        'provider' => ['@id' => route('home').'#business'],
        'areaServed' => collect(array_keys(config('service-areas')))->map(fn ($region) => ['@type' => 'AdministrativeArea', 'name' => $region])->prepend(['@type' => 'Country', 'name' => 'Indonesia'])->values()->all(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($localService, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
@endpush
@section('community')
@if($pageRecord->slug === 'umrah-jogja')
<section class="container section"><h2>Pilihan paket umrah Safir Jogja</h2><p>Jadwal berikut berasal dari paket yang dipublikasikan tim. Konfirmasikan harga akhir dan ketersediaan sebelum mendaftar.</p><div class="cards">@forelse($localPackages as $package)<x-package-card :package="$package" />@empty<p>Jadwal keberangkatan berikutnya sedang disiapkan. Hubungi tim untuk informasi terbaru.</p>@endforelse</div><a class="text-link" href="{{ route('packages.index') }}">Bandingkan semua paket umrah</a></section>
@endif
@endsection
@section('content')<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy6_001') }}</p><h1>{{ $pageRecord->title }}</h1>@if($pageRecord->intro)<p>{{ $pageRecord->intro }}</p>@endif</section><section class="container section prose"><x-markdown :text="$site->resolve($pageRecord->body)" /></section>@endsection
