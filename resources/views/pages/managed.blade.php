@extends('layouts.site')
@section('community')
@if($pageRecord->slug === 'umrah-jogja')
<section class="container section"><h2>Pilihan paket umrah Safir Jogja</h2><p>Jadwal berikut berasal dari paket yang dipublikasikan tim. Konfirmasikan harga akhir dan ketersediaan sebelum mendaftar.</p><div class="cards">@forelse($localPackages as $package)<x-package-card :package="$package" />@empty<p>Jadwal keberangkatan berikutnya sedang disiapkan. Hubungi tim untuk informasi terbaru.</p>@endforelse</div><a class="text-link" href="{{ route('packages.index') }}">Bandingkan semua paket umrah</a></section>
@endif
@endsection
@section('content')<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy6_001') }}</p><h1>{{ $pageRecord->title }}</h1>@if($pageRecord->intro)<p>{{ $pageRecord->intro }}</p>@endif</section><section class="container section prose"><x-markdown :text="$site->resolve($pageRecord->body)" /></section>@endsection
