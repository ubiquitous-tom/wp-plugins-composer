jQuery(document).ready(function($) {
    initializeStripeElements(local_vars.stripe_key);
    $('#account-renewal form').on('submit', function(event) {
        var submit_button = jQuery(this).find('button.submit');
        var submit_button_width = submit_button.width();
        var submit_button_content = submit_button.html();
        submit_button.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        submit_button.width(submit_button_width);
        // We have an initialized 
        event.preventDefault();
        // Remove any errors we have
        jQuery('.alert').remove();

        var name_on_card = jQuery('input#card-name').val();
        var billing_first_name = jQuery('input#billing-first-name').val();
        var billing_last_name = jQuery('input#billing-last-name').val();
        var plan = jQuery('select#sub-plan').val();
        var promo_code = jQuery('input#promo-code').val();

        stripe.createToken(cardNumber, {
            name: name_on_card
        }).then(function (result) {
            if (result.error) {
                submit_button.prop('disabled', false).html(submit_button_content);
                var alert = jQuery(document.createElement('div')).addClass("alert alert-danger fade in").html(result.error.message);
                alert.insertAfter(jQuery('#account-renewal header'));
            } else {
                var stripe_token = result.token;

                jQuery.post(
                    local_vars.ajax_url,
                    {
                        'action': 'update_subscription',
                        'promo_code': promo_code,
                        'billing_first_name': billing_first_name,
                        'billing_last_name': billing_last_name,
                        'name_on_card': name_on_card,
                        'stripe_token': stripe_token.id,
                        'subscription_plan': plan
                    },
                    function (response) {
                        submit_button.prop('disabled', false).html(submit_button_content);
                        var alert = jQuery(document.createElement('div')).addClass("alert fade in");
                        if (response.success == false) {
                            alert.addClass('alert-danger').html(response.error);
                        } else {
                            document.cookie = "ATVSessionCookie=; expires=-1; path=/";
                            alert.addClass('alert-success').html('Thanks for your purchase. You\'d get redirected and asked to signin again.');
                            var timer = setTimeout(function() {
                                window.location.replace("/signin");
                            }, 10000);
                        }
                        alert.insertAfter(jQuery('#account-renewal header'));
                    }
                )
            }
        });
    });
    $('#promo-code').on('blur', function(event) {
        promo_code = $(this).val();
        if(promo_code) {
            jQuery('.alert').remove();
            jQuery(document.createElement('div')).addClass("alert alert-info")
                .append(jQuery(document.createElement('i')).addClass("fa fa-spinner fa-spin fa-fw"))
                .append('Applying promo code')
                .insertAfter(jQuery('#account-renewal header'));
            jQuery.get(
                local_vars.ajax_url,
                {
                    'action': 'apply_renewal_promo',
                    'promo_code': promo_code
                },
                function (response) {
                    jQuery('.alert').remove();
                    var alert = jQuery(document.createElement('div')).addClass("alert fade in");
                    if (response.success == false) {
                        alert.addClass('alert-danger').html(response.error);
                    } else {
                        planOptions = [];
                        alert.addClass('alert-success')
                            .html('<i class="fa fa-check-circle-o fa-lg"></i>Promo ' + promo_code.toUpperCase() + ' applied.');
                        response.plans.forEach(function(plan) {
                            planOptions.push(
                                jQuery('<option>')
                                    .attr('value', plan.name.toLowerCase())
                                    .html(`${capitalizeFirstLetter(plan.name)} - $${plan.cost}`)
                            );
                        });
                        jQuery('#sub-plan').empty().append(planOptions);
                    }
                    alert.insertAfter(jQuery('#account-renewal header'));
                }
            )
        }
    })
});

function initializeStripeElements(stripeKey) {
    stripe = Stripe(stripeKey);
    var style = {
        base: {
            lineHeight: '20px',
            fontFamily: 'Avenir, Nunito, sans-serif',
            fontSize: '14px',
        }
    }
    var elements = stripe.elements();
    cardNumber = elements.create('cardNumber', { 
        style: style
    });
    cardNumber.mount('#card-number');

    cardExpiray = elements.create('cardExpiry', {
        style: style
    });
    cardExpiray.mount('#card-expiration');

    cardCvc = elements.create('cardCvc', {
        style: style
    });
    cardCvc.mount('#card-cvc');

    cardNumber.addEventListener('change', function (event) {
        console.log(event);
    })
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}