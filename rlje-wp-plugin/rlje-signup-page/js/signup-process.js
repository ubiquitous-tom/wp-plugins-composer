var sessionId;
var promo;
var stripe;
var card;
var cardNeeded = true;

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

function showStepTwo() {
    if( typeof promo !== 'undefined' && Object.keys(promo).length && promo.MembershipTerm == 12 && promo.MembershipTermType == 'MONTH') {
        cardNeeded = false;
    }
    // Update the url hash to indicate step2
    history.replaceState(undefined, undefined, "#createaccount");
    var signup_form = jQuery('form.signup');
    // Remove log in and Start signup buttons for some reason
    jQuery('.navbar-header.navbar-right').hide();
    // Render second step.
    // Mark step 2 as active
    jQuery('#progress-steps .step').removeClass('active');
    jQuery('#progress-steps .step:nth-child(2)').addClass('active');
    // Update header and side description of the page
    jQuery('#signup h3').html('Last step and then start watching');
    jQuery('#signup p.side').html('Watch free for 7 days. Just $4.99/month after that. No commitment: cancel within 7 days to avoid payment.');
    // Attach new handlers to handle form submit event
    signup_form.off('submit');
    // Update the form fields
    signup_form.empty();

    if( typeof promo !== 'undefined' && Object.keys(promo).length) {
        jQuery(document.createElement('div'))
            .addClass('alert alert-success')
            .html('<i class="fa fa-check-circle-o fa-lg"></i>Promo ' + promo.PromotionCode + ' applied.' )
            .appendTo(signup_form);
    }
    
    var profile_header = jQuery(document.createElement('h4')).addClass('form-head').html('Your profile');

    var first_name_label = jQuery(document.createElement('label')).attr('for', 'user-first-name').html('First Name *');
    var first_name_input = jQuery(document.createElement('input')).addClass('form-control').attr(
        {
            id: 'user-first-name',
            name: 'user_first_name',
            type: 'text'
        }
    ).prop('required', true);
    var first_name_group = jQuery(document.createElement('div')).addClass('form-group').append(first_name_label, first_name_input);

    var last_name_label = jQuery(document.createElement('label')).attr('for', 'user-last-name').html('Last Name *');
    var last_name_input = jQuery(document.createElement('input')).addClass('form-control').attr(
        {
            id: 'user-last-name',
            name: 'user_last_name',
            type: 'text'
        }
    ).prop('required', true);
    var last_name_group = jQuery(document.createElement('div')).addClass('form-group').append(last_name_label, last_name_input);

    var countries = [
        jQuery(document.createElement('option')).attr('value', 'US').html('United States'),
        jQuery(document.createElement('option')).attr('value', 'CA').html('Canada'),
        jQuery(document.createElement('option')).attr('value', 'AS').html('American Samoa'),
        jQuery(document.createElement('option')).attr('value', 'AR').html('Argentina')
    ]
    var country_label = jQuery(document.createElement('label')).html('Country *');
    var country_select = jQuery(document.createElement('select')).addClass('form-control').prop('disabled', true).append(countries);
    var country_group = jQuery(document.createElement('div')).addClass('form-group').append(country_label, country_select);

    signup_form
        .append(profile_header)
        .append(jQuery(document.createElement('div')).addClass('signup-form-group')
            .append(first_name_group, last_name_group, country_group)
        );

    var plan_header = jQuery(document.createElement('h4')).addClass('form-head').html('Plan &amp; Payment');
    var plan_payment_section = jQuery(document.createElement('div')).addClass('signup-form-group')
    .append(plan_header);

    if( cardNeeded ) {
        var plan_desc = jQuery(document.createElement('p')).html('Please select a plan for when your 7 day FREE TRIAL comes to an end. You can cancel anytime before your trial ends and you will not be charged.');

        var plans = renderPlans();
        plan_payment_section
            .append(plan_desc, plans)
            .append(renderPaymentFields());
    } else {
        var profile_header = jQuery(document.createElement('div')).addClass('alert alert-info').html('Yearly plan selected based on promo code.' );
        plan_payment_section.append(profile_header);
    }

    // Submit button
    var step_two_submit = jQuery(document.createElement('button')).addClass('submit-step btn btn-primary btn-lg center-block').html('Signup');
    signup_form.append(plan_payment_section, step_two_submit);
    
    if(cardNeeded) {
        // Initialize Stripe so it can mount it's iframes
        initializeStripeElements(signup_vars.stripe_key);
    }

    // Attach form submit handler
    signup_form.on('submit', submitStepTwo);
}

function submitStepTwo(event) {
    var submit_button = jQuery(this).find('button.submit-step');
    var submit_button_width = submit_button.width();
    var submit_button_content = submit_button.html();
    submit_button.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
    submit_button.width(submit_button_width);
    // We have an initialized 
    event.preventDefault();
    // Remove any errors we have
    jQuery('.alert').remove();

    var name_on_card = jQuery('input#card-name').val();
    var billing_first_name = jQuery('input#user-first-name').val();
    var billing_last_name = jQuery('input#user-last-name').val();
    var plan = 'monthly';
    if(true == jQuery('input[type="radio"]#yearly-plan').prop('checked') || cardNeeded === false ) {
        plan = 'yearly';
    }
    if(typeof promo !== 'undefined' && Object.keys(promo).length) {
        promo_code = promo.PromotionCode;
    } else {
        promo_code = null;
    }


    if(cardNeeded) {
        stripe.createToken(cardNumber, {
            name: name_on_card
        }).then(function (result) {
            if (result.error) {
                submit_button.prop('disabled', false).html(submit_button_content);
                var alert = jQuery(document.createElement('div')).addClass("row alert alert-danger fade in").append(jQuery(document.createElement('p'))).html(result.error.message);
                alert.insertAfter(jQuery('#progress-steps'));
            } else {
                var stripe_token = result.token;
                createMembership({
                    'session_id': sessionId,
                    'promo_code': promo_code,
                    'billing_first_name': billing_first_name,
                    'billing_last_name': billing_last_name,
                    'name_on_card': name_on_card,
                    'stripe_token': stripe_token.id,
                    'subscription_plan': plan
                });
            }
        });
    } else {
        createMembership({
            'session_id': sessionId,
            'promo_code': promo_code,
            'billing_first_name': billing_first_name,
            'billing_last_name': billing_last_name,
            'subscription_plan': plan
        });
    }
}

function showStepThree() {
    // Update the url hash to indicate step3
    history.replaceState(undefined, undefined, "#finish");
    // Render last step.
    // Mark last step as active
    jQuery('#progress-steps .step').removeClass('active');
    jQuery('#progress-steps .step:nth-child(3)').addClass('active');
    // Update header and side description of the page
    jQuery('#signup h3').html('Congratulations! Sign up complete.');
    jQuery('#signup p.side').html('Thanks for trying UMC! We think you\'ll love it.');
    // Get rid of the form.
    jQuery('form.signup').remove();
    // Adds the player and its parents to the dom
    var brightCoveVideo = jQuery(document.createElement('video')).attr({
        'id': 'umc-about',
        'data-video-id': signup_vars.bc_video_id,
        'data-account': signup_vars.bc_account_id,
        'data-player': signup_vars.bc_player_id,
        'data-embed': 'default'
    }).addClass('video-js embed-responsive-item').prop('controls', true);
    var brightCoveVideoContainer = jQuery(document.createElement('div')).addClass('row')
        .append(
            jQuery(document.createElement('div')).addClass('embed-responsive embed-responsive-16by9').append(brightCoveVideo)
        )
        .appendTo('#signup .container > div');

    var watchNowContainer = jQuery(document.createElement('div')).addClass('row')
        .append(
            jQuery(document.createElement('div')).addClass('text-center')
            .append(
                jQuery(document.createElement('a'))
                .attr(
                    {
                        'id': 'finish-singup',
                        'href': '/signin',
                        'role': 'button'
                    }
                )
                .addClass('btn btn-primary btn-lg')
                .html('Start watching now')
            )
            
        );

    jQuery('#signup .container > div').append(brightCoveVideoContainer, watchNowContainer);
    initBrightCove();
}

function renderPlans() {
    return jQuery(document.createElement('fieldset'))
    .append(
        jQuery(document.createElement('div')).attr('id', 'plans')
        .append(
            jQuery(document.createElement('div')).addClass('plan monthly')
            .append(
                jQuery(document.createElement('input')).prop('checked', true).attr({
                    'id': 'monthly-plan',
                    'name': 'sub-plan',
                    'type': 'radio',
                    'value': 'monthly'
                })
            )
            .append(
                jQuery(document.createElement('label')).attr({
                    'for': 'monthly-plan'
                })
                .append(
                    jQuery(document.createElement('p')).addClass('plan-header text-center').html('Monthly plan')
                )
                .append(
                    jQuery(document.createElement('p')).html('Join UMC for only $4.99 a month')
                )
                .append(
                    jQuery(document.createElement('p'))
                    .append(
                        jQuery(document.createElement('span')).addClass('price').html('$4.99'),
                        ' / 30 day'
                    )
                )
                .append(
                    jQuery(document.createElement('p')).html('Cancel at any time.')
                )
            )
        )
        .append(
            jQuery(document.createElement('div')).addClass('plan yearly')
            .append(
                jQuery(document.createElement('input')).attr({
                    'id': 'yearly-plan',
                    'name': 'sub-plan',
                    'type': 'radio',
                    'value': 'yearly'
                })
                
            )
            .append(
                jQuery(document.createElement('label')).attr({
                    'for': 'yearly-plan'
                })
                .append(
                    jQuery(document.createElement('p')).addClass('plan-header text-center').html('Yearly plan')
                )
                .append(
                    jQuery(document.createElement('p')).html('Get a whole year of UMC for the price of 10 months')
                )
                .append(
                    jQuery(document.createElement('p'))
                    .append(
                        jQuery(document.createElement('span')).addClass('price').html('$49.99'),
                        ' / 12 month'
                    )
                )
                .append(
                    jQuery(document.createElement('p')).html('Cancel at any time.')
                )
            )
        )
    )
    
}

function renderPaymentFields() {
    // Name on card field
    var card_name_label = jQuery(document.createElement('label')).attr('for', 'card-name').html('Name on Card *');
    var card_name_input = jQuery(document.createElement('input')).addClass('form-control').attr({
        id: 'card-name',
        name: 'card_name',
        type: 'text'
    }).prop('required', true);
    var card_name_group = jQuery(document.createElement('div')).addClass('form-group').append(card_name_label, card_name_input);

    // Card number input elements for strip js to mount to
    var card_label = jQuery(document.createElement('label')).attr('for', 'card-number').html('Card number *');
    var card_container = jQuery(document.createElement('div')).attr('id', 'card-number');
    var card_number_element = jQuery(document.createElement('div')).addClass('form-group').append(card_label, card_container);

    // Expiration date and CVC fields
    var card_expiration_element = jQuery(document.createElement('div')).addClass('col-sm-6')
    .append(
        jQuery(document.createElement('div')).addClass('form-group')
        .append(
            jQuery(document.createElement('label')).attr('for', 'card-expiration').html('Expiration *')
        )
        .append(
            jQuery(document.createElement('div')).attr('id', 'card-expiration')
        )
    )
    

    var card_cvc_element = jQuery(document.createElement('div')).addClass('col-sm-6')
    .append(
        jQuery(document.createElement('div')).addClass('form-group')
        .append(
            jQuery(document.createElement('label')).attr('for', 'card-cvc').html('CVC *')
        )
        .append(
            jQuery(document.createElement('div')).attr('id', 'card-cvc')
        )
    )
    

    var some_row = jQuery(document.createElement('div')).addClass('row').append(card_expiration_element, card_cvc_element);
    return [card_name_group, card_number_element, some_row];
}

function createMembership(params) {
    params.action = 'create_membership';
    console.log(params);
    jQuery.post(
        signup_vars.ajax_url,
        params,
        function (response) {
            if (response.success == false) {
                submit_button.prop('disabled', false).html(submit_button_content);
                var alert = jQuery(document.createElement('div')).addClass("row alert alert-danger fade in").append(jQuery(document.createElement('p'))).html(response.error);
                alert.insertAfter(jQuery('#progress-steps'));
            } else {
                showStepThree();
            }
        }
    )
}

function initBrightCove() {
    bc(document.getElementById('umc-about'));
    videojs('umc-about').ready(function() {
        myPlayer = this;
        myPlayer.pause();
    })
}

jQuery(document).ready(function ($) {
    $('form.signup').on('submit', function (event) {
        event.preventDefault();
        $('.alert').remove();
        var valid = true;
        var email = $(this).find('input#signup-email').val();
        var email_confirm = $(this).find('input#signup-email-confirm').val();
        var password = $(this).find('input#signup-password').val();
        var password_confirm = $(this).find('input#signup-password-confirm').val();
        var promo_code = $(this).find('input#promo-code').val();
        if (email !== email_confirm) {
            valid = false;
            var alert = $(document.createElement('div')).addClass("row alert alert-danger fade in").append('<p>Email addresses don\'t match</p>');
            alert.insertAfter($('#progress-steps'));
        }
        if (password !== password_confirm) {
            valid = false;
            var alert = $(document.createElement('div')).addClass("row alert alert-danger fade in").append('<p>Passwords don\'t match</p>');
            alert.insertAfter($('#progress-steps'));
        }
        if (valid) {
            var submit_button = $(this).find('button.submit-step');
            var submit_button_width = submit_button.width();
            var submit_button_content = submit_button.html();
            submit_button.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i>');
            submit_button.width(submit_button_width);
            $.post(
                signup_vars.ajax_url,
                {
                    'action': 'initialize_account',
                    'email_address': email,
                    'password': password,
                    "promo_code" : promo_code
                },
                function (response) {
                    if (response.success == false) {
                        var alert = $(document.createElement('div')).addClass("row alert alert-danger fade in").html(response.error);
                        alert.insertAfter($('#progress-steps'));
                        submit_button.prop('disabled', false).html(submit_button_content);
                    } else {
                        // Update the form to show step two fields
                        sessionId = response.session_id;
                        promo = response.promo;
                        showStepTwo();
                    }
                }
            )
        }
    })
});