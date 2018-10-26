(function($) {
	'use strict';

	$(document).ready(function() {
		// Navbar slide left
		var sideslider = $('[data-toggle=collapse-side]');
		var sel = sideslider.attr('data-target');
		var sel2 = sideslider.attr('data-target-2');
		sideslider.click(function(event){
		$(sel).toggleClass('in');
		$(sel2).toggleClass('out');});

		//feature carousel state change
		// $('#myCarousel').carousel({
		// 	interval: 40000
		// });

		// Carousel state change for various screen sizes

		(function(){
			$('.carousel-respond-slide .item').each(function(){
				var itemToClone = $(this);

				for (var i=1;i<4;i++) {
					itemToClone = itemToClone.next();

					// wrap around if at end of item collection
					if (!itemToClone.length) {
						itemToClone = $(this).siblings(':first');
					}


					itemToClone.children(':first-child').clone()
						.addClass('cloneditem-'+(i))
						.appendTo($(this));
				}
			});
		}());

		$('.overlay .carousel-button:last-child').on('click', function(){
			$('.item.active img').remove();
		});


	});
})(jQuery);
