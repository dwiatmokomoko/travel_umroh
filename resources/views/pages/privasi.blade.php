@extends('layouts.site')
@section('content')
<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy8_001') }}</p><h1>{{ $site->text('copy8_002') }}</h1></section><article class="container section prose"><h2>{{ $site->text('copy8_003') }}</h2><p>{{ $site->text('copy8_004') }}</p><h2>{{ $site->text('copy8_005') }}</h2><p>{{ $site->text('copy8_006') }}</p><h2>{{ $site->text('copy8_007') }}</h2><p>{{ $site->text('copy8_008') }}</p><h2>{{ $site->text('copy8_009') }}</h2><p>Untuk pertanyaan tentang data dalam percakapan konsultasi, hubungi {{ config('safir.contact') }} melalui nomor {{ config('safir.phone') }}. Hindari memasukkan nomor paspor, data kesehatan, atau informasi pembayaran ke formulir konsultasi umum.</p></article>
@endsection
