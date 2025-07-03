{{--
    Title: Texte et image
    Category: blocs-Tolle
    Icon: <svg viewBox="0 0 640 512"><path d="M32 32C14.3 32 0 46.3 0 64l0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64 144 0 0 384-64 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l160 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-64 0 0-384 144 0 0 64c0 8.8 7.2 16 16 16s16-7.2 16-16l0-64c0-17.7-14.3-32-32-32L32 32zM352 224c-17.7 0-32 14.3-32 32l0 32c0 8.8 7.2 16 16 16s16-7.2 16-16l0-32 112 0 0 192-48 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l128 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-48 0 0-192 112 0 0 32c0 8.8 7.2 16 16 16s16-7.2 16-16l0-32c0-17.7-14.3-32-32-32l-128 0-128 0z"/></svg>
--}}

@php
    extract(get_fields());

    $bgColor = $section_options['bgColor'];
    $reverse = $section_options['reverse'];

    if (!empty($image)) {
        $image_id = $image['id'];

        $image = wp_get_attachment_image(
            $image_id,
            'large',
            false,
            [
                'class' => 'w-full insight ghost delay--2',
                'loading' => 'lazy',
                'srcset' => wp_get_attachment_image_srcset($image_id),
                'sizes'  => '(max-width: 768px) 100vw, (max-width: 1500px) 1500px, 2500px'
            ]
        );
    }
@endphp

<section 
    @if (!empty($block['anchor'])) id="{{ $block['anchor'] }}" @endif
    data-{{ $block['id'] }} 
    class="{{ $bgColor == 'white' ? 'bg-white text-red-700' : 'bg-gray-100 text-blue-900' }} block-{{ $block['classes'] }}"
>
    <div class="container">
        <div class="padd">
            <div class="wrap">
                <div class="flex flex-col-reverse {{ $reverse == true ? 'md:flex-row-reverse' : 'md:flex-row' }} gap-14">
                    @if (! empty($title) || ! empty($text))
                        <div class="w-full {{ ! empty($image) ? 'md:w-1/2' : '' }}">
                            <div class="sticky top-16">
                                {{-- TITLE --}}
                                @include('components.title')

                                {{-- TEXT --}}
                                @if (! empty($text))
                                    <div class="insight ghost delay--2">
                                        {!! $text !!}
                                    </div>
                                @endif

                                {{-- BUTTONS --}}
                                @include('components.buttons')
                            </div>
                        </div>
                    @endif

                    {{-- IMAGE --}}
                    @if (! empty($image))
                        <div class="w-full {{ ! empty($title) || ! empty($text) ? 'md:w-1/2' : '' }}">
                            <div class="sticky top-16">
                                {!! $image !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>