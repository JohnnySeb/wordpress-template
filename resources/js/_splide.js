import Splide from '@splidejs/splide';

if (document.querySelectorAll('.splide').length > 0) {
    const servicesSplides = document.querySelectorAll('.splide');

    servicesSplides.forEach((splide) => {
        const splideInstance = new Splide(splide, {
            type: 'loop',
            perPage: 2,
            perMove: 1,
            arrows: true,
            pagination: false,
            gap: '1.5em',
            drag: 'free',
            snap: true,
            speed: 1000,
            breakpoints: {
                1024: {
                    fixedWidth: 300,
                    fixedHeight: 401,
                    gap: '1em',
                },
                768: {
                    fixedWidth: 250,
                    fixedHeight: 334,
                },
                480: {
                    fixedWidth: 200,
                    fixedHeight: 268,
                    gap: '1em',
                },
            },
        });

        splideInstance.mount();
    });
}