@extends('layouts.site')
@section('content')
<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy7_001') }}</p><h1>{{ $site->text('copy7_002') }}<br><em>{{ $site->text('copy7_003') }}</em></h1><p>{{ $site->text('copy7_004') }}</p></section>
<article class="container section prose"><h2>{{ $site->text('copy7_005') }}</h2><p>{{ $site->text('copy7_006') }}</p><h2>{{ $site->text('copy7_007') }}</h2><p>{{ $site->text('copy7_008') }}</p><h2>{{ $site->text('copy7_009') }}</h2><p>{{ $site->text('copy7_010') }}</p><h2>{{ $site->text('copy7_011') }}</h2><p>{{ $site->text('copy7_012') }}</p><h2>{{ $site->text('copy7_013') }}</h2><p>{{ $site->text('copy7_014') }}</p><h2>{{ $site->text('copy7_015') }}</h2>@include('partials.faq')<p><a class="button" href="{{ route('packages.index') }}">{{ $site->text('copy7_016') }}</a></p></article>
@endsection
