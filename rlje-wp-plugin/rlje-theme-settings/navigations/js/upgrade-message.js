(function($, docCookies) {
	'use strict';

	$( '#upgrade-dismiss' ).on( 'click', function(e) {
		e.preventDefault();

		$( '#upgradeMessage' ).remove();
		if (upgrade_message) {
			docCookies.setItem( upgrade_mesage.key, upgrade_message.value, upgrade_mesage.end, upgrade_mesage.path, upgrade_mesage.domain );
		}
	});

})(jQuery, docCookies);
