<div class="flex items-center gap-2">
    <a
        href="http://www.facebook.com/sharer/sharer.php?u={{ urlencode(get_permalink()) }}"
        target="_blank"
        title="<?= __('Share on Facebook', 'tolle') ?>"
        class="group inline-block relative w-10 h-10 rounded-full border bg-black hover:bg-white border-black duration-300"
    >
        @include('svg.share-facebook')
    </a>

    <a
        href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(get_permalink()) }}"
        target="_blank"
        title="<?= __('Share on LinkedIn', 'tolle') ?>"
        class="group inline-block relative w-10 h-10 rounded-full border bg-black hover:bg-white border-black duration-300"
    >
        @include('svg.share-linkedin')
    </a>

    <a
        href="mailto:?subject=<?= __('I want to share this article with you', 'tolle') ?>&amp;body=<?= __('Look at this website:', 'tolle') ?> {{ urlencode(get_permalink()) }}"
        target="_blank"
        title="<?= __('Share by email', 'tolle') ?>"
        class="group inline-block relative w-10 h-10 rounded-full border bg-black hover:bg-white border-black duration-300"
    >
        @include('svg.share-email')
    </a>
</div>