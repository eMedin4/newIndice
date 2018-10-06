<div class="wrap">
    <a href="{{ route('movie', $record->movie->slug) }}">
            <img src="{{ asset('/movimages/backgrounds/std/' . $record->movie->slug . '.jpg') }}" alt="">
            <h3 class="h3">{{ $record->movie->title }}</h3>
        </a>
        
        <section class="info">
            
            <ul class="meta">
                <li class="country country-{{ $record->movie->country }}"></li>
                <li class="year">{{ $record->movie->year }}</li>
                <li class="stars">{!! $record->movie->avg_stars !!}</li>
                <li class="popularity-tag">popular</li>
                <li class="popularity popularity-{{ $record->movie->fa_popularity["class"] }}"><span class="popularity-inner"></span></li>
            </ul>
            
            <p class="excerpt">{{ $record->movie->excerpt200 }}</p>
                <div class="program">
                    <div class="channel"><span class="icon-tv"></span> {{ $record->channel }}</div>
                    <time><span class="icon-clock"></span> {!! $record->format_time !!}</time>
                </div>

        {{-- @include('includes.develop-data') --}}

    </section>
</div>