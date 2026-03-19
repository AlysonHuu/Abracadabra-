let index = 0;
const items = document.querySelectorAll('.movie-card');

function updateCarousel() {
    if (items.length === 0) return;
    
    items.forEach((item, i) => {
        item.classList.remove('active', 'left', 'right');
        if (i === index) {
            item.classList.add('active');
        } else if (i === (index - 1 + items.length) % items.length) {
            item.classList.add('left');
        } else if (i === (index + 1) % items.length) {
            item.classList.add('right');
        }
    });
}

function nextSlide() {
    index = (index + 1) % items.length;
    updateCarousel();
}

function prevSlide() {
    index = (index - 1 + items.length) % items.length;
    updateCarousel();
}

updateCarousel();