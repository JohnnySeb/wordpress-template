@if (! empty($titleGroup['title']))
    <{{ $titleGroup['tag'] ?? 'h2' }} 
        class="{{ $titleGroup['tag_style'] ?? 'h2' }} insight ghost delay--2"
    >
        {{ $titleGroup['title'] }}
    </{{ $titleGroup['tag'] ?? 'h2' }} >
@endif