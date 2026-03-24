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

var swiper2 = new Swiper(".currentSwiper", {
  slidesPerView: "auto",
  spaceBetween: 24,
  navigation: {
      nextEl: ".swiper-button-next.current-course",
      prevEl: ".swiper-button-prev.current-course",
  },
});

var swiper3 = new Swiper(".upcoming-learning-slider", {
  slidesPerView: "auto",
  spaceBetween: 24,
  navigation: {
      nextEl: ".swiper-button-next.upcoming-course",
      prevEl: ".swiper-button-prev.upcoming-course",
  },
});

var swiper4 = new Swiper(".trendSwiper", {
    slidesPerView: 4,
    spaceBetween: 32,
    navigation: {
        nextEl: ".swiper-button-next.trend-course",
        prevEl: ".swiper-button-prev.trend-course",
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        991: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
        1440: {
            slidesPerView: 4,
            spaceBetween: 32,
        }
    }
});

var swiper5 = new Swiper(".topSwiper", {
    slidesPerView: 3,
    spaceBetween: 32,
    navigation: {
        nextEl: ".swiper-button-next.top-course",
        prevEl: ".swiper-button-prev.top-course",
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        991: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
    }
});