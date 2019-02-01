jQuery(document).ready(function($) {

    $('form#user-update-email').on('submit', function(event) {
        event.preventDefault();
        $('.alert').remove();

        var btn = $(this).find('button');
        // Disable the button and show an indicator that we are cancelling
        btn.prop('disabled', true);
        btn.text('Updating ...');

        var email = $( this ).find('[name=new-email]').val();
        var email_repeat = $( this ).find('[name=new-email-confirm]').val();
        if(email !== email_repeat) {
            var alert = $(document.createElement('div')).addClass( "alert alert-danger fade in" );
            alert.append('Two email addresses won\'t match').insertAfter($('.section-header'));
        } else {
            $.post(
                account_management_vars.ajax_url,
                {
                    'action': 'user_update_email',
                    'new_email': email
                },
                function(response) {
                    var alert = $(document.createElement('div')).addClass( "alert fade in" );
                    if(response.success) {
                        alert.addClass("alert-success");
                        alert.append("E-mail successfully updated.");
                    } else {
                        alert.addClass("alert-danger");
                        alert.append("There was an error updating your E-mail address. We've been notified and working to fix it.");
                    }
                    alert.insertAfter($('.section-header'));
                    btn.text('Update E-mail')
                    btn.prop('disabled', false);
                }
            );
        }
    });
    
    $('form#user-change-password').on('submit', function(event) {
        event.preventDefault();
        $('.alert').remove();

        var btn = $(this).find('button');
        // Disable the button and show an indicator that we are cancelling
        btn.prop('disabled', true);
        btn.text('Updating ...');

        var pass = $( this ).find('[name=new-password]').val();
        var pass_repeat = $( this ).find('[name=new-password-confirm]').val();
        if(pass !== pass_repeat) {
            var alert = $(document.createElement('div')).addClass( "alert alert-danger fade in" );
            alert.append('Two passwords won\'t match').insertAfter($('.section-header'));
        } else {
            $.post(
                account_management_vars.ajax_url,
                {
                    'action': 'user_update_password',
                    'new_password': pass
                },
                function(response) {
                    var alert = $(document.createElement('div')).addClass( "alert fade in" );
                    if(response.success) {
                        alert.addClass("alert-success");
                        alert.append("Password successfully updated.");
                    } else {
                        alert.addClass("alert-danger");
                        alert.append("There was an error updating your password. We've been notified and working to fix it.");
                    }
                    alert.insertAfter($('.section-header'));
                    btn.text('Update Password')
                    btn.prop('disabled', false);
                }
            );
        }
    });
    $('button#confirmCancel').on('click', function() {
        var btn = $(this);
        // Disable the button and show an indicator that we are cancelling
        btn.prop('disabled', true);
        btn.text('cancelling ...');
        // Hit the API to cancel user subscription
        $.post(
            account_management_vars.ajax_url,
            {
                'action': 'cancel_sub',
                'session_id': account_management_vars.session_id
            },
            function(response) {
                $('#cancelMembershipModal').modal('hide');
                var alert = $(document.createElement('div')).addClass( "alert fade in" );
                if(response.success) {
                    alert.addClass("alert-success");
                    alert.append("Your subscription has been cancelled.");
                } else {
                    alert.addClass("alert-danger");
                    alert.append("There was an error cancelling your account. We've been notified and working to fix it.");
                }
                $("#account-settings section").html(alert);
            }
        );
    });

    $('form#apply-code').on('submit', function(event) {
        event.preventDefault();
        $('.alert').remove();
        var promoCode = $( this ).find('[name=promo-code]').val();
        var btn = $(this).find('button#apply-promo-code');
        // Disable the button and show an indicator that we are cancelling
        btn.prop('disabled', true);
        btn.text('Applying code ...');
        $.post(
            account_management_vars.ajax_url,
            {
                'action': 'apply_promo_code',
                'promo_code': promoCode,
            },
            function(response) {
                var alert = $(document.createElement('div')).addClass( "alert fade in" );
                if(response.success) {
                    alert.addClass("alert-success");
                    alert.append("Promo code has been applied.");
                } else if (response.error) {
                    alert.addClass("alert-danger");
                    alert.append( response.error );
                } else {
                    alert.addClass("alert-danger");
                    alert.append("Invalid promo code.");
                }
                $("#account-settings section").prepend(alert);
                btn.prop('disabled', false);
                btn.text('Apply Code');
            }
        )
    })
});