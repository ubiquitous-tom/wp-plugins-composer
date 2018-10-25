(function($) {
	'use strict';

	$(document).ready(function() {
		var type = $('#rightsline-auth-type').val();
		$('.auth-type[data-auth-type="' + type + '"]').removeClass('hidden');

		$('#rightsline-auth-type').on('change', function(e) {
			e.preventDefault();
			$('.auth-type').addClass('hidden');
			var type = $(this).val();
			$('.auth-type[data-auth-type="' + type + '"]').removeClass('hidden');
		});

	});
})(jQuery);
