@extends('layouts.site')
@section('content')
<section class="page-heading container"><p class="eyebrow">{{ $site->text('copy3_001') }}</p><h1>{{ $site->text('copy3_002') }}<br><em>{{ $site->text('copy3_003') }}</em></h1><p>{{ $site->text('copy3_004') }}<br>{{ $site->text('copy3_005') }}</p></section>
<section class="container section catalogue"><form class="filters" action="{{ route('packages.index') }}" method="get"><label>{{ $site->text('copy3_006') }}<select name="month"><option value="">{{ $site->text('copy3_007') }}</option>@foreach($months as $month)<option value="{{ $month->departure_date->format('Y-m') }}" @selected(request('month') === $month->departure_date->format('Y-m'))>{{ $month->departure_date->translatedFormat('F Y') }}</option>@endforeach</select></label><label>{{ $site->text('copy3_008') }}<select name="days"><option value="">{{ $site->text('copy3_009') }}</option>@foreach($durations as $duration)<option value="{{ $duration }}" @selected((string) request('days') === (string) $duration)>{{ $duration }} hari</option>@endforeach</select></label><label>{{ $site->text('copy3_012') }}<select name="sort"><option value="date" @selected(request('sort') !== 'price')>{{ $site->text('copy3_013') }}</option><option value="price" @selected(request('sort') === 'price')>{{ $site->text('copy3_014') }}</option></select></label><button class="button" type="submit">{{ $site->text('copy3_015') }}</button></form>
@if($errors->any())<div class="notice" role="alert">{{ $errors->first() }}</div>@endif
<div class="results-heading"><p>{{ $packages->count() }} pilihan keberangkatan</p><a href="{{ route('packages.index') }}">{{ $site->text('copy3_017') }}</a></div><div class="cards">@forelse($packages as $package)<x-package-card :package="$package" />@empty<div class="empty-state"><h2>{{ $site->text('copy3_018') }}</h2><p>{{ $site->text('copy3_019') }}</p>@if($site->pageVisible('kontak'))<a class="button" href="{{ route('contact') }}">{{ $site->text('copy3_020') }}</a>@endif</div>@endforelse</div><p class="fine package-disclaimer">{{ $site->text('copy3_021') }}</p></section>
<section class="container section" aria-labelledby="planning-title">
<h2 id="planning-title">Rencanakan umrah per bulan: {{ $planningMonths->first()['label'] }}–{{ $planningMonths->last()['label'] }}</h2>
<p>Pilih bulan rencana perjalanan dalam lima tahun ke depan. Kalender ini membantu perencanaan; harga, kursi, dan kepastian keberangkatan mengikuti paket yang dipublikasikan serta konfirmasi admin.</p>
@foreach($planningMonths->groupBy('year') as $year => $yearMonths)
<details @if($loop->first) open @endif><summary>Rencana umrah {{ $year }}</summary><ul>
@foreach($yearMonths as $planningMonth)
<li><a href="{{ route('packages.index', ['month' => $planningMonth['key']]) }}">Umrah {{ $planningMonth['label'] }}</a> — {{ $planningMonth['count'] ? $planningMonth['count'].' paket dipublikasikan; konfirmasikan ketersediaan' : 'Jadwal belum dipublikasikan' }}</li>
@endforeach
</ul></details>
@endforeach
@if($site->pageVisible('kontak'))<p><a href="{{ route('contact') }}">Konsultasikan bulan pilihan Anda dengan tim Safir</a>.</p>@endif
</section>
@endsection
