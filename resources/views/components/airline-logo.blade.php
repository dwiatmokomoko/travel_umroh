@props(['airline'])
@php($airlineLogo = match(strtolower(trim($airline))) { 'garuda indonesia', 'garuda' => 'logo-garuda.png', 'lion air', 'lion' => 'logo-lion.png', 'saudia', 'saudia airlines', 'saudi arabian airlines', 'saudi airlines' => 'logo-saudia.png', default => null })
@if($airlineLogo)<img class="airline-logo" src="{{ asset('images/'.$airlineLogo) }}" alt="{{ $airline }}" width="120" height="40" loading="lazy">@endif
