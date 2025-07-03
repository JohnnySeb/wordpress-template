<footer class="bg-black">
    <div class="container">
        <div class="wrap">
            {{-- LEGAL --}}
            <div class="flex flex-col sm:flex-row justify-center sm:justify-between items-center gap-1.5 py-6 insight ghost">
                <div class="flex flex-col sm:flex-row justify-center sm:justify-start gap-4 sm:gap-12">
                    <a
                        href="<?= get_privacy_policy_url() ?>"
                        title="<?= __('Privacy policy','tolle') ?>"
                        class="text-14 text-gray-400 font-medium text-center sm:text-left hover:text-white duration-300"
                    >
                        <?= __('Privacy policy','tolle') ?>
                    </a>
                </div>

                {{-- @TODO: Modifier l'URL du utm_source --}}
                <a
                    href="https://www.agencetolle.com/?utm_source=git-portes-et-fenetres&utm_medium=web&utm_campaign=client"
                    target="_blank"
                    title="<?= __('Tollé Web Agency - Application and website development','tolle') ?>"
                    class="group flex items-center duration-300 mt-3 sm:mt-0"
                >

                <span class="text-14 text-gray-400 mr-2 sm:group-hover:opacity-75 sm:opacity-0 duration-500 sm:group-hover:translate-x-0 sm:translate-x-[10%] origin-right">
                    <?= __('Website by','tolle') ?>
                </span>

                @include('svg.tolle')
                </a>
            </div>
        </div>
    </div>
</footer>
