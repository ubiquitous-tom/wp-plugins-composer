(function($){
	'use strict';
	var timeOut,
			player = '<video id="preview-trailer" data-account="3392051363001" data-video-id="%videoId" data-player="default" data-embed="default" class="video-js" controls><\/video><script src="//players.brightcove.net/3392051363001/default_default/index.min.js"><\/script>',
			loading = '<img src="/wp-admin/images/spinner-2x.gif" alt="loading..." />',
			$featureImg = $('.featuredImg-preview'),
			$loading = $('.featureImg-loading');

	$('#_atv_trailer_id').keyup(function(elm) {
			clearTimeout(timeOut);
			$('#preview-container').html(loading);
			timeOut = setTimeout(function(){
					var newPlayer = player.replace('%videoId', elm.target.value);
					$('#preview-container').html(newPlayer);
			}, 3000);
	});

	$featureImg.on('error', function(){
		$featureImg.attr('src', 'https://placeholdit.imgix.net/~text?txtsize=33&txt=No%20Image&w=350&h=250');
		$loading.hide();
	});
	$featureImg.on('load', function(){
		$loading.hide();
	});

	$('#_atv_featuredImageUrl').keyup(function(elm){
		if (elm.target.value.length > 4) {
			clearTimeout(timeOut);
			$loading.show();
			timeOut = setTimeout(function() {
				$featureImg.attr('src', elm.target.value);
			}, 3000);
		} else {
			$featureImg.attr('src', 'https://placeholdit.imgix.net/~text?txtsize=33&txt=No%20Image&w=350&h=250');
		}
	});
})(jQuery);
