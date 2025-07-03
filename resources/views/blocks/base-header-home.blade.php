{{--
    Title: Entête principale
    Category: blocs-Tolle
    Icon: <svg viewBox="0 0 576 512"><path d="M64 112c-8.8 0-16 7.2-16 16l0 256c0 8.8 7.2 16 16 16l19.8 0 80.8-110.2c4.5-6.2 11.7-9.8 19.4-9.8s14.8 3.6 19.4 9.8L232.8 330l83.1-127.1c4.4-6.8 12-10.9 20.1-10.9s15.7 4.1 20.1 10.9L485 400l27 0c8.8 0 16-7.2 16-16l0-256c0-8.8-7.2-16-16-16L64 112zM96 448l-32 0c-35.3 0-64-28.7-64-64L0 128C0 92.7 28.7 64 64 64l448 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-40 0-200 0-72 0L96 448zm64-288a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg>
--}}

@php
    extract(get_fields());

    if (!empty($image)) {
        $image_id = $image['id'];

        $image = wp_get_attachment_image(
            $image_id,
            'full',
            false,
            [
                'class' => 'absolute top-0 left-0 bottom-0 w-full h-[100%] object-cover object-center z-10',
                'srcset' => wp_get_attachment_image_srcset($image_id),
                'sizes'  => '(max-width: 768px) 100vw, (max-width: 1500px) 1500px, 2500px'
            ]
        );
    }
@endphp

<header 
    @if (!empty($block['anchor'])) id="{{ $block['anchor'] }}" @endif 
    data-{{ $block['id'] }} 
    class="relative block-{{ $block['classes'] }} overflow-hidden"
>
    <div class="container relative z-30">
        <div class="padd">
            <div class="wrap">
                <div class="flex flex-col items-center py-24">
                    <div class="w-full">
                        {{-- TITLE --}}
                        @if (! empty($title))
                            <h1 class="font-semibold !text-white leading-tight insight ghost delay--2">
                                {{ $title }}
                            </h1>
                        @endif
                        
                        {{-- TEXT --}}
                        @if (! empty($text))
                            <p class="text-white insight ghost delay--2">
                                {{ $text }}
                            </p>
                        @endif
                        
                        {{-- BUTTONS --}}
                        @include('components.buttons')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($image))
        <div class="absolute inset-0 bg-black opacity-40 z-20"></div>
        {!! $image !!}
    @endif
</header>