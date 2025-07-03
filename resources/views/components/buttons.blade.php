@if (! empty($buttonsClone['buttons']))
    <div class="btn-wrapper insight ghost {{ $extraClasses ?? '' }}">
        @foreach ($buttonsClone['buttons'] as $button)
            @php
                $icon = $button['icon'] ?? '';

                if (!empty($icon)) {
                    $icon_id = $icon['id'];
            
                    $icon = wp_get_attachment_image(
                        $icon_id,
                        'full',
                        false,
                        [
                            'class' => 'w-[15px] h-[15px]',
                            'loading' => 'lazy',
                            'srcset' => wp_get_attachment_image_srcset($icon_id),
                        ]
                    );
                }
            @endphp

            <a
                href="{{ $button['link']['url'] ?? '' }}"
                target="{{ $button['link']['target'] ?? '_self' }}"
                title="{{ $button['link']['title'] ?? '' }}"
                class="group btn {{ $button['variant'] ? '--' . $button['variant'] : '' }}"
            >
                @if (! empty($button['icon']) && $button['icon_position'] == 'before')
                    {!! $icon !!}
                @endif
        
                @if (! empty($button['link']['title']))
                    <span class="txt">
                        {{ $button['link']['title'] }}
                    </span>
                @endif

                @if (! empty($button['icon']) && $button['icon_position'] == 'after')
                    {!! $icon !!}
                @endif
            </a>
        @endforeach
    </div>
@endif