import Swiper from 'swiper/bundle';

const swiper = new Swiper(".stlms-similar-course-slider", {
    loop: true,
    slidesPerView: 1,
    spaceBetween: 10,
    navigation: {
      nextEl: ".stlms-sc-slider-next",
      prevEl: ".stlms-sc-slider-prev",
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
        spaceBetween: 32,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 32,
      },
    },
});
