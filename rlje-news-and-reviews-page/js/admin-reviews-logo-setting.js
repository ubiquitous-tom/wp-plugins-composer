(function($){
	'use strict';

	$('.rwsOptionTitle').each(updateNewsOptionsKeys);

	function updateNewsOptionsKeys(key, elem) {
		$(elem).parents('tr').attr('id',key);
		if(key == 0) {
			$(elem).parents('tbody').attr('id', 'news_options');
		}
	}

	$('#addReviewOption').click(addNewsOption);

	function addNewsOption(event) {
		event.preventDefault();
		var $lastTr = $('#news_options > tr:last-child'),
			id = $lastTr.attr('id'),
			$newTr = $lastTr.clone().attr('id', id*1+1);
		//Update ids and clean fields
		$newTr.find('input').each(function(key,elem){
			updateFields(key,elem);
			emptyFields(elem);
		});
		$newTr.find('.removeReviewOption').on('click', removeReviewOption);
		$newTr.find('.uploadBtn').on('click', uploadBtn);
		$lastTr.after($newTr);
	}

	function updateFields(key,elm) {
		var id = $(elm).parents('tr[id]').attr('id'),
			name = $(elm).attr('name');
		if(typeof name !== 'undefined') {
			$(elm).attr('name', name.replace( /(.+)\[[\d]+\](.+)/, "$1["+id+"]$2"));
		}
	}

	function emptyFields(elem) {
		$(elem).val('');
	}

	$('.removeReviewOption').click(removeReviewOption);

	function removeReviewOption(event) {
		event.preventDefault();
		$(event.target).parents('tr').remove();
		//Reorder the tr element id.
		$('.rwsOptionTitle').each(function(key, elem) {
			$(elem).parents('tr').attr('id', key);
		});
		$('#news_options td > input').each(updateFields);
	}

	//Upload image action button
	$('.uploadBtn').click(uploadBtn);

	function uploadBtn(e) {
		e.preventDefault();
		var setImageUrl = function(value) {
				var imageUrl = value.replace('http', 'https');
				$(e.target).parent().find('input.uploadImage').val(imageUrl);
			},
			image = wp.media({
			title: 'Upload Image',
			multiple: false // True if you want to upload multiple files at once.
		})
		.open()
		.on('select', function(){
			// Return the selected image from the Media Uploader (the result is an object).
			var uploaded_image = image.state().get('selection').first();
			// Convert uploaded_image to a JSON object doing it more easy to handle.
			var image_url = uploaded_image.toJSON().url;
			// Set the url value to the proper input field.
			setImageUrl(image_url);
		});
	}

})(jQuery);
