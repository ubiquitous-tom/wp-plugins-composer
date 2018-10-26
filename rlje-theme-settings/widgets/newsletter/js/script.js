(function($) {
	'use strict';

	$('#signup-newsletter-widget form').on('submit', function(e) {
		e.preventDefault();
		var $emailInput = $('#signup-newsletter-email'),
			$emailButtonInput = $('#signup-newsletter-button'),
			email = $.trim($emailInput.val()),
			messageDiv = $('<div>').addClass('alert'),
			$message = $('#signup-newsletter-message');

		$.ajax({
			type: 'POST',
			url: rlje_newsletter_widget_object.ajax_url,
			data: {
				'action' : 'rlje_newsletter_signup',
				'email': email,
				'token': rlje_newsletter_widget_object.token
			},
			success: function(resp) {
				var data = resp.data;
				$message.append(messageDiv.addClass(data.type).html(data.message));
				$emailInput.prop('disabled', true);
				$emailButtonInput.prop('disabled', true);
			},
			error: function(error) {
				var data = error.data;
				$message.append(messageDiv.addClass(data.type).html(data.message));
			}
		});
	});
})(jQuery);
