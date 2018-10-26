(function($) {
	'use strict';

	$(document).ready(function() {
		var video = document.querySelector('#trailer-video');
		var embedcode = video.dataset.embedcode;
		var hasvideo = video.dataset.hasvideo;

		// Only append the video if it isn't yet appended.
		// if (!hasvideo) {
		// 	video.insertAdjacentHTML('afterbegin', embedcode);
		// 	video.dataset.hasvideo = 'true';
		// }
	});
})(jQuery);
