(function($) {
  $(document).ready(function() {
    $('#watchlist-button').on('click', '#watchlistActionButton', function(e) {
      e.preventDefault();
      var action = $(this).attr('data-action'),
        $button = $(this),
        button_text,
        toggle,
        data = {
          action: action,
          franchise_id: franchise_object.franchise_id,
          nonce: franchise_object.nonce
        };
      $.post(franchise_object.ajax_url, data, function(resp) {
        var message = resp.data.message;
        if (message === 'OK') {
          toggle = 'add';
          button_text = 'Add To Watchlist';
        } else {
          toggle = 'remove';
          button_text = 'Remove From Watchlist';
        }
        $button.html(button_text).attr('data-action', toggle);
      });
    });
  });
})(jQuery);
