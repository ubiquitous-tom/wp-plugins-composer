(function($){
	'use strict';

	$('.mkgType').change(changeType);

	function changeType(event) {
		var elm = event.target,
			$newsCarousel = $(elm).parents('.news-carousel'),
			$parentId = $newsCarousel.attr('data-id'),
			$parent = $('#news-carousel-' + $parentId),
			$mkgFranchiseId = $parent.find('.mkgFranchiseId'),
			$mkgExternalLink = $parent.find('.mkgExternalLink'),
			$mkgSrc = $parent.find('.mkgSrc'),
			// $uploadBtn = $parent.find('.uploadBtn'),
			defaultImageSrcSize = $mkgSrc.find('input').attr('data-imageSrcSize'),
			defaultVideoSrcSize = $mkgSrc.find('input').attr('data-videoSrcSize'),
			defaultExtImageSrcSize = $mkgSrc.find('input').attr('data-extImageSrcSize'),
			defaultImagePlaceholder = $mkgSrc.find('input').attr('data-imagePlaceholder'),
			defaultVideoPlaceholder = $mkgSrc.find('input').attr('data-videoPlaceholder');
		if($(elm).val() === 'image') {
			$mkgExternalLink.hide();
			$mkgFranchiseId.show();
			// $uploadBtn.show();
			$mkgExternalLink.find('input').attr('value', '');
			$mkgSrc.find('input').attr({'size': defaultImageSrcSize, 'placeholder': defaultImagePlaceholder, 'value': ''});
		} else if($(elm).val() === 'extImage') {
			$mkgFranchiseId.hide();
			$mkgExternalLink.show();
			// $uploadBtn.show();
			$mkgFranchiseId.find('input').attr('value', '');
			$mkgSrc.find('input').attr({'size': defaultExtImageSrcSize, 'placeholder': defaultImagePlaceholder, 'value': ''});
		} else {
			$mkgFranchiseId.hide();
			$mkgExternalLink.hide();
			// $uploadBtn.hide();
			$mkgFranchiseId.find('input').attr('value', '');
			$mkgExternalLink.find('input').attr('value', '');
			$mkgSrc.find('input').attr({'size': defaultVideoSrcSize, 'placeholder': defaultVideoPlaceholder, 'value': ''});
		}
	};

	$('.mkgType').parents('tbody').addClass('sortable news_section');
	$('.lnwsText').parents('.form-table > tbody').addClass('sortable');

	$('.sortable').sortable({
		placeholder: 'sortable-placeholder',
		connectWith: '.sortable',
		start: function(e, ui){
			ui.placeholder.height(ui.item.height());
		}
	});

	$('.sortable').sortable({
		update: function( event, ui ) {
			$(event.target).find('> tr > td').each(function(key, elem) {
				$(elem).parents('tr.ui-sortable-handle').attr('id', key);
				$(elem).children().each(updateFields);
			});
		}
	});

	function updateFields(key,elm) {
		var id = $(elm).parents('tr[id]').attr('id'),
			name = $(elm).attr('name');
		if(typeof name !== 'undefined') {
			$(elm).attr('name', name.replace( /(.+)\[[\d]+\](.+)/, '$1['+id+"]$2"));
		}
		else{
			$(elm).find('td *').each(updateFields);
		}
	}

	$('.mkgType').each(function(key,elem){
		$(elem).parents('tr').addClass('ui-state-default').attr('id',key);
	});

	$('.lnwsText').each(function(key,elem){
		$(elem).parents('.ui-sortable-handle').addClass('ui-state-default').attr('id',key);
	});

	//Add break lines as separetor after to News section.
	// $('.news_section').parent().after('<div class="section-separator"><br/><br/><br/></div>');

	//Upload image action button
	// $('.uploadBtn').click(function(e) {
	// 	e.preventDefault();
	// 	var setImageUrl = function(value) {
	// 			var imageUrl = value.replace('http', 'https');
	// 			$(e.target).parent().find('input.uploadImage').val(imageUrl);
	// 		},
	// 		image = wp.media({
	// 		title: 'Upload Image',
	// 		multiple: false // True if you want to upload multiple files at once.
	// 	})
	// 	.open()
	// 	.on('select', function(){
	// 		// Return the selected image from the Media Uploader (the result is an object).
	// 		var uploaded_image = image.state().get('selection').first();
	// 		// Convert uploaded_image to a JSON object doing it more easy to handle.
	// 		var image_url = uploaded_image.toJSON().url;
	// 		// Set the url value to the proper input field.
	// 		setImageUrl(image_url);
	// 	});
	// });
	$(document).ready(function() {
		$('.news-carousel').each(function(ind, el) {
			var type = $(el).find('.mkgType').val();
			switch(type) {
				case 'image':
					$(el).find('.mkgExternalLink').hide();
					break;
				case 'extImage':
				$(el).find('.mkgFranchiseId').hide();
					break;
				default:
				$(el).find('.mkgFranchiseId').hide();
				$(el).find('.mkgExternalLink').hide();
			}
			var brightcoveVideo = $(el).find('.brightcove-public-player');
			if ( brightcoveVideo.length ) {
				bc(brightcoveVideo[0]);
			}
		});
	});
})(jQuery);
