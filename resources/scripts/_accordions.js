const accordionTrigger = document.querySelectorAll('.accordionTrigger');
const bodyWrapper = document.querySelector('.bodyWrapper');

accordionTrigger.forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const accordionBody = trigger.nextElementSibling;

        if (!trigger.classList.contains('active')) {
            const activeaccordionBody = document.querySelector(
                '.accordionTrigger.active + .accordionBody'
            );
            if (activeaccordionBody) {
                activeaccordionBody.style.maxHeight = '0';
                activeaccordionBody.previousElementSibling.classList.remove(
                    'active'
                );
            }

            trigger.classList.add('active');
            accordionBody.style.maxHeight = accordionBody.scrollHeight + 'px';

            setTimeout(() => {
                const accordionParent = trigger.closest('.accordion');
                if (accordionParent) {
                    accordionParent.scrollIntoView({ behavior: 'smooth' });
                }
            }, 500);

            setTimeout(() => {
                bodyWrapper.scrollTop = bodyWrapper.scrollTop + 1;
            }, 800);
        } else {
            trigger.classList.remove('active');
            accordionBody.style.maxHeight = '0';
        }
    });
});
