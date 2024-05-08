const animate = () => {
  const inSightElements = document.querySelectorAll('.insight');

  function handleIntersection(entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting || entry.boundingClientRect.y < 0) {
        entry.target.classList.add('alive');
      } else {
        entry.target.classList.remove('alive');
      }
    });
  }

  const observer = new IntersectionObserver(handleIntersection, {
    root: null,
    threshold: 0,
  });

  inSightElements.forEach((elem) => {
    observer.observe(elem);
  });
};

window.addEventListener('DOMContentLoaded', () => {
  animate();
});

export default animate;