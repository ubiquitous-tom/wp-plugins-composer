jQuery(document).on('carousel_pagination.add_new_item_image_url', function(event, image_url, value) {
	var new_image_url = image_url;
	if (value.image_h) {
		var image_h = value.image_h.split('?');
		var new_image = image_url.split('?');
		new_image_url = new_image[0].replace(value.franchiseID + '_avatar', image_h[0]);
		new_image_url = new_image_url + '?t=titled-avatars&w=400&h=225';
	}
	// console.log('add_new_item_image_url', image_url, value, new_image_url);
	return new_image_url;
});

jQuery(document).on('carousel_pagination.second_page_image_url', function(event, image_url, value) {
	var new_image_url = image_url;
	if (value.image_h) {
		var image_h = value.image_h.split('?');
		var new_image = image_url.split('?');
		new_image_url = new_image[0].replace('{{ID}}_avatar', image_h[0]);
		new_image_url = new_image_url + '?t=titled-avatars&w=400&h=225';
	}
	// console.log('second_page_image_url', image_url, value, new_image_url);
	return new_image_url;
});
