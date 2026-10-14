<div class="hero-carousel" data-carousel role="region" aria-roledescription="carousel" aria-label="Galeri perjalanan Safir">
    @forelse($heroSlides as $slide)
        <div class="hero-slide" data-slide @if(!$loop->first) hidden @endif role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} dari {{ $loop->count }}">
            <img class="hero-image" src="{{ route('media', $slide->image) }}" alt="{{ $slide->image_alt }}" width="1920" height="1560" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
            <div class="image-caption"><span>{{ $slide->title }}</span><strong>{{ $slide->caption }}</strong></div>
        </div>
    @empty
        <div class="hero-slide"><img class="hero-image" src="{{ $site->image('hero_image', 'images/madinah.jpg') }}" alt="{{ $site->text('access_001') }}" width="1920" height="1560" fetchpriority="high"><div class="image-caption"><span>{{ $site->text('copy1_009') }}</span><strong>{{ $site->text('copy1_010') }}</strong></div></div>
    @endforelse
    @if($heroSlides->count() > 1)
        <div class="carousel-controls" data-carousel-controls hidden>
            <button type="button" data-previous aria-label="Slide sebelumnya">←</button>
            <div class="carousel-dots">@foreach($heroSlides as $slide)<button type="button" data-go="{{ $loop->index }}" aria-label="Tampilkan slide {{ $loop->iteration }}: {{ $slide->title }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"></button>@endforeach</div>
            <button type="button" data-next aria-label="Slide berikutnya">→</button>
            <button type="button" data-pause aria-label="Jeda pergantian slide">Jeda</button>
        </div>
    @endif
</div>
