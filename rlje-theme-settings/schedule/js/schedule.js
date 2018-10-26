// (function() {
//   var videos = document.querySelectorAll('.video');
//   for (var i = 0; i < videos.length; i++) {
//     // Closure to call the playVideo function.
//     if (videos[i].querySelector('.js-play')) {
//       videos[i].querySelector('.js-play').onclick = (function(index) {
//         return function() {
//           loadVideo(this, videos[index]);
//         };
//       })(i);
//     }
//   }

//   function loadVideo(button, video) {
//     var embedcode = video.dataset.embedcode;
//     var hasvideo = video.dataset.hasvideo;

//     // Only append the video if it isn't yet appended.
//     if (!hasvideo) {
//       video.insertAdjacentHTML('afterbegin', embedcode);
//       video.dataset.hasvideo = 'true';
//       button.remove();
//     }
//   }
// })();

// (function($) {
// 	'use strict';

// 	$(document).ready(function() {
// 		var video = document.querySelector('#trailer-video');
// 		// var embedcode = video.dataset.embedcode;
// 		// var hasvideo = video.dataset.hasvideo;

// 		// Only append the video if it isn't yet appended.
// 		// if (!hasvideo) {
// 		// 	video.insertAdjacentHTML('afterbegin', embedcode);
// 		// 	video.dataset.hasvideo = 'true';
// 		// }
// 	});
// })(jQuery);

(function($) {
  $(document).ready(function() {
    var player = videojs('brightcove-schedule-trailer-player');
    $('#schedule-trailer-video .js-play').on('click', function(e) {
      e.preventDefault();
      $('#brightcove-schedule-trailer-player').removeClass('hidden');
      player.play();
    });
  });
})(jQuery);
