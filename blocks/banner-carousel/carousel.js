
/**
 *	Find blocks & init the swiper
 */
window.addEventListener('DOMContentLoaded', (event) => {
	// Find & loop all blocks
	var blocks = document.getElementsByClassName('wp-block-banner-carousel');
	Array.prototype.forEach.call(blocks, function (block) {

		numslides = block.getElementsByClassName('swiper-slide').length;

		// Prepare some options.
		per_view = 1;
		// Can be altered via localized script.
		if (tailored_banner_carousel.per_view) per_view = tailored_banner_carousel.per_view;
		mid_per_view = per_view;
		if (mid_per_view > 2) mid_per_view = per_view - 1;

		// Init swiper
		var args = {
			slidesPerView: per_view,
			spaceBetween: 0,
			loop: true,
			autoplay: {
				delay: 5000,
			},
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			},
			// Navigation arrows
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			effect: 'slide',	// slide, fade, cube, coverflow, flip
			breakpoints: {
				100: {
					slidesPerView: 1,
				},
				768: {
					slidesPerView: mid_per_view,
				},
				1024: {
					slidesPerView: per_view,
				},
			}
		};
		var swiper = new Swiper(block.getElementsByClassName('swiper-container').item(0), args);

		// Remove navigation if there is only one slide.
		if (numslides == 1) {
			var elements = block.querySelectorAll('.swiper-pagination, .swiper-button-next, .swiper-button-prev');
			elements.forEach((element) => {
				element.remove();
			});
			swiper.allowSlidePrev = false;
			swiper.allowSlideNext = false;
		}

	});
});
