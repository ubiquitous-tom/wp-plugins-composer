(function($) {
	// bcswipe.js mobile touch swipe for all carousels
	$.fn.bcSwipe = function(settings) {
		var config = { threshold: 50 };
		if (settings) {
			$.extend(config, settings);
		}

		this.each(function() {
			var stillMoving = false;
			var start;

			if ('ontouchstart' in document.documentElement) {
				this.addEventListener('touchstart', onTouchStart, false);
			}

			function onTouchStart(e) {
				if (e.touches.length == 1) {
					start = e.touches[0].pageX;
					stillMoving = true;
					this.addEventListener('touchmove', onTouchMove, false);
				}
			}

			function onTouchMove(e) {
				if (stillMoving) {
					var x = e.touches[0].pageX;
					var difference = start - x;
					if (Math.abs(difference) >= config.threshold) {
						cancelTouch();
						if (difference > 0) {
							$(this).carousel('next');
						}
						else {
							$(this).carousel('prev');
						}
					}
				}
			}

			function cancelTouch() {
				this.removeEventListener('touchmove', onTouchMove);
				start = null;
				stillMoving = false;
			}
		});

		return this;
	};

	$('.carousel').bcSwipe({ threshold: 55,  maxTimeThreshold:10 });
})(jQuery);
