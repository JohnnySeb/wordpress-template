{{--
    Title: Accordéons
    Category: blocs-Tolle
    Icon: <svg viewBox="0 0 448 512"><path class="fa-secondary" opacity=".4" d="M39 332c-11 13.8-8.8 33.9 5 45L204 505c5.8 4.7 12.9 7 20 7s14.1-2.3 20-7L404 377c13.8-11 16-31.2 5-45s-31.2-16-45-5L224 439 84 327c-5.9-4.7-13-7-20-7c-9.4 0-18.7 4.1-25 12z"/><path class="fa-primary" d="M204 7c11.7-9.3 28.3-9.3 40 0L404 135c13.8 11 16 31.2 5 45s-31.2 16-45 5L224 73 84 185c-13.8 11-33.9 8.8-45-5s-8.8-33.9 5-45L204 7z"/></svg>
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

                {{-- ACCORDIONS --}}
                @if (!empty($accordions))
                    <div class="accordion-group">
                        @foreach ($accordions as $index => $accordion)
                            @php
                                $accordionId = 'accordion-' . $block['id'] . '-' . $index;
                            @endphp

                            <div class="accordion insight ghost delay--2">
                                {{-- TRIGGER --}}
                                <button
                                    type="button"
                                    class="accordion-header text-lg md:text-xl lg:text-2xl font-semibold text-primary flex justify-between w-full text-left"
                                    aria-expanded="false"
                                    aria-controls="{{ $accordionId }}"
                                >
                                    {{ $accordion['title'] }}

                                    <span class="plusMinus">
                                        <span></span>
                                        <span></span>
                                    </span>
                                </button>

                                {{-- CONTENT --}}
                                <div id="{{ $accordionId }}" class="accordion-body" role="region" aria-labelledby="{{ $accordionId }}-label">
                                    <div class="pb-8">
                                        <div class="generic-content">
                                            {!! $accordion['content'] !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- @if (!empty($accordions))
    @php
        $faqSchema = [];
        foreach ($accordions as $index => $accordion) {
            $faqSchema[] = [
                '@type' => 'Question',
                'name' => trim($accordion['title']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags($accordion['content']))
                ]
            ];
        }
    @endphp

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqSchema
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif --}}
