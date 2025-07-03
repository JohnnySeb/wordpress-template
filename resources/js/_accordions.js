const accordionHeader = document.querySelectorAll('.accordion-header');

accordionHeader.forEach(accordionHeader => {
    accordionHeader.addEventListener('click', () => {
        const accordionBody = accordionHeader.nextElementSibling;

        if (!accordionHeader.classList.contains('active')) {
            const activeAccordionHeader = document.querySelector('.accordion-header.active + .accordion-body');
            if (activeAccordionHeader) {
                activeAccordionHeader.style.maxHeight = '0';
                activeAccordionHeader.previousElementSibling.classList.remove('active');
            }

            accordionHeader.classList.add('active');
            accordionBody.style.maxHeight = accordionBody.scrollHeight + 'px';
        } else {
            accordionHeader.classList.remove('active');
            accordionBody.style.maxHeight = '0';
        }
    });
});