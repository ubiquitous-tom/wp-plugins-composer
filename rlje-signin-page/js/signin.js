jQuery(document).ready(function($) {
    $('form.signin').on('submit', function(event) {
        event.preventDefault();
        $('.alert').remove();
        var email = $(this).find('#login-email').val();
        var password = $(this).find('#login-password').val();
        var submit_button = $(this).find('button.btn');
        var submit_button_width = submit_button.width();
        var submit_button_content = submit_button.html();
        submit_button.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        submit_button.width(submit_button_width);
        $.post(
            signin_vars.ajax_url,
            {
                'action': 'signin_user',
                'email_address': email,
                'password': password
            },
            function (response) {
                if (response.success == false) {
                    var alert = $(document.createElement('div')).addClass("row alert alert-danger fade in").html(response.error);
                    alert.insertAfter($('#signin header'));
                    submit_button.prop('disabled', false).html(submit_button_content);
                } else {
                    if(response.status === 'expired' ) {
                        window.location.replace("/account/renew");
                    } else {
                        window.location.replace("/");
                    }
                    
                }
            }
        )
    });
});