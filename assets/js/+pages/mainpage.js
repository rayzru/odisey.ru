jQuery(function ($) {
	$('.owl-carousel')
		.owlCarousel({
			items: 2,
			margin: 10,
			loop: true,
			lazyLoad: true,
			autoplay: true
		});

	$('.partnersLogos')
		.owlCarousel({
			items: 5,
			slideBy: 'page',
			loop: true,
			autoplay: true,
			nav: false,
			dots: false,
			center: true,
			lazyLoad: true
		});
});
