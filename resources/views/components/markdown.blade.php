@props(['text'])
<div class="markdown">{!! \Illuminate\Support\Str::markdown($text ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
