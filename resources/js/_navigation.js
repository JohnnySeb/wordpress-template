/* eslint-disable prettier/prettier */
const body = document.querySelector('body');
const burger = document.querySelector('.burger');
const navigation = document.querySelector('header#main-nav');

let lastScrollDistance = 0;
let newScrollPosition = 0;

// BODY PADDING TOP
function bodyPaddingTop() {
    if (navigation) {
        body.style.paddingTop = `${navigation.offsetHeight}px`;
    }
}

// NAVIGATION HIDE ON SCROLL
function navigationOnScroll() {
    if (navigation) {
        const currentScrollDistance = window.scrollY;

        if (!body.classList.contains('menu-shown')) {
            if (currentScrollDistance < 500) {
                navigation.classList.remove('hideNav');
            } else if (currentScrollDistance > lastScrollDistance) {
                navigation.classList.add('hideNav');
            } else {
                navigation.classList.remove('hideNav');
            }

            lastScrollDistance = currentScrollDistance;
        }
    }
}

// KILL MENU
function killMenu() {
    body.classList.remove('menu-shown');
}

// KILL MENU ON SCROLL
function killMenuOnScroll() {
    if (body.classList.contains('menu-shown')) {
        if (Math.abs(window.scrollY - newScrollPosition) > 250) {
            killMenu();
        }
    }
}

// BURGER MENU
if (burger) {
    burger.addEventListener('click', () => {
        body.classList.toggle('menu-shown');

        if(!body.classList.contains('menu-shown')) {
            killMenu();
        }
        else {
            newScrollPosition = window.scrollY;
        }
    });
}

// EVENTS
document.addEventListener('DOMContentLoaded', () => {
    bodyPaddingTop();
});

window.addEventListener('scroll', () => {
    navigationOnScroll();
    killMenuOnScroll();
});

window.addEventListener('resize', () => {
    bodyPaddingTop();
});
