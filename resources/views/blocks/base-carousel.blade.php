{{--
    Title: Carousel
    Category: blocs-Tolle
    Icon:<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M51.8 160.4c-3.7 2.1-4.9 6.8-2.8 10.5l167 289.3c2.1 3.7 6.8 4.9 10.5 2.8L419.4 351.6c3.7-2.1 4.9-6.8 2.8-10.5L255.2 51.8c-2.1-3.7-6.8-4.9-10.5-2.8L51.8 160.4zM7.5 194.9c-15.4-26.6-6.3-60.7 20.4-76.1L220.7 7.5c26.6-15.4 60.7-6.3 76.1 20.4l167 289.3c15.4 26.6 6.2 60.7-20.4 76.1L250.5 504.5c-26.6 15.4-60.7 6.2-76.1-20.4L7.5 194.9zm451.9 226c41.9-24.2 56.3-77.8 32.1-119.8L354.7 64.2c1.7-.2 3.5-.2 5.3-.2l224 0c30.9 0 56 25.1 56 56l0 336c0 30.9-25.1 56-56 56l-224 0c-13.7 0-26.2-4.9-35.9-13l135.3-78.1z"/></svg>
--}}

@php
    extract(get_fields());
@endphp

<section
    @if (!empty($block['anchor'])) id="{{ $block['anchor'] }}" @endif
    data-{{ $block['id'] }}
    class="bg-white block-{{ $block['classes'] }}"
>
    <div class="container">
        <div class="padd">
            <div class="wrap">
                {{-- TITLE --}}
                @include('components.title')
                
                <div class="splide insight ghost" role="group">
                    <div class="splide__track">
                        <div class="splide__list">
                            @if (! empty($slides))
                                @foreach ($slides as $slide)
                                    @php
                                        $slideClasses = 'relative overflow-hidden bg-cover bg-center bg-no-repeat rounded-xl py-10 px-14';
                                    @endphp
                                    
                                    {{-- SI LA SLIDE A UN LIEN --}}
                                    @if (! empty($slide['link']))
                                        <a
                                            href="{{ $slide['link']['url'] }}"
                                            target="{{ $slide['link']['target'] ?: '_self' }}"
                                            title="{{ $slide['link']['title'] ?: $slide['title'] ?: '' }}"
                                            class="splide__slide {{ $slideClasses }}"
                                            style="background-image: url('{{ $slide['image']['url'] }}');"
                                        >
                                    @else
                                        <div 
                                            class="splide__slide {{ $slideClasses }}"
                                            style="background-image: url('{{ $slide['image']['url'] }}');"
                                        >
                                    @endif
                                        <div class="absolute inset-0 bg-gradient-to-b from-white/70 to-white/40 z-10"></div>

                                        <div class="relative z-20">
                                            <h3 class="h3">
                                                {{ $slide['title'] ?? '' }}
                                            </h3>
                                            {{ $slide['text'] ?? '' }}
                                        </div>
                                    @if (! empty($slide['link']))
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>