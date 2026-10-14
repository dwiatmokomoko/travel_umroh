<div class="consultant-list">
    @foreach($site->consultants() as $consultant)
        <div class="consultant-card">
            <span class="consultant-avatar" aria-hidden="true">{{ mb_substr($consultant['name'], 0, 1) }}</span>
            <div><strong>{{ $consultant['name'] }}</strong><small>Konsultan Safir Jogja</small></div>
            @if($consultant['available'])
                <a class="button small" href="https://wa.me/{{ $consultant['phone'] }}?text={{ rawurlencode('Assalamu’alaikum, saya ingin konsultasi umrah bersama '.$consultant['name'].'.') }}" data-consultant-link target="_blank" rel="noopener noreferrer">WhatsApp<span class="sr-only"> {{ $consultant['name'] }}</span></a>
            @else
                <small>Belum tersedia</small>
            @endif
        </div>
    @endforeach
</div>
