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
    $('form.password-reset').on('submit', function(event){
        event.preventDefault();
        $('.alert').remove();
        var email = $(this).find('#email').val();
        var submit_button = $(this).find('button.btn');
        var submit_button_width = submit_button.width();
        var submit_button_content = submit_button.html();
        submit_button.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        submit_button.width(submit_button_width);
        $.post(
            signin_vars.ajax_url,
            {
                'action': 'reset_password',
                'email_address': email
            },
            function (response) {
                var alert = $(document.createElement('div'))
                    .addClass("alert");
                if( response.success ) {
                    alert.addClass('alert-success')
                        .html(tmpl('tmpl-reset-success'));
                } else {
                    alert
                        .addClass('alert-danger')
                        .html(response.error);
                }
                alert.insertAfter($('#forgotpassword header'));
                submit_button.prop('disabled', false).html(submit_button_content);
            }
        )
    });
});