var guardpost_link = "<a href='http://blog.mailgun.com/post/free-email-validation-api-for-web-forms/' target='_blank'>Validated using our API</a>";

jQuery().ready(function() {
    jQuery('#mailgun_subscribe_btn').click(function(e) {
        
        var emailfield = jQuery('#mailgun_subscribe_email');
        if (!emailfield.val()) {
            rdiv = jQuery('#mgsubscribe_validate_status');
            rdiv.text("Please enter an email address");
        }
        return false;
    });
    
    jQuery('#mailgun_subscribe_email').mailgun_validator({
        api_key: '40733976039a7f5c8193eea7618d0313aababe20',
        in_progress: mgsubscribe_validation_in_progress,
        success: mgsubscribe_validation_success,
        error: mgsubscribe_validation_error
    });
});

jQuery.fn.mailgun_validator = function(options) {
    return this.each(function() {
        jQuery(this).focusout(function() {
            run_validator(jQuery(this).val(), options);
        });
    });
};


function run_validator(address_text, options) {
    // don't run validator without input
    if (!address_text) {
        return;
    }

    // length check
    if (address_text.length > 512) {
        error_message = 'Email exceeds allowable length of 512.';
        if (options && options.error) {
            options.error(error_message);
        }
        else {
            console.log(error_message);
        }
        return;
    }

    // validator is in progress
    if (options && options.in_progress) {
        options.in_progress();
    }

    if (options && options.api_key == undefined) {
        console.log('Please pass in api_key to mailgun_validator.')
    }

    var success = false;

    // make ajax call to get validation results
    jQuery.getJSON('https://api:' + options.api_key + '@api.mailgun.net/v2/address/validate?callback=?', {
        address: address_text,
    }).done(function(data, text_status, jq_xhr) {
        success = true;
        if (options && options.success) {
            options.success(data);
        }
    }).error(function(jq_xhr, text_status, error_thrown) {
        success = true;
        if (options && options.error) {
            options.error(jq_xhr);
        }
        else {
            console.log(jq_xhr);
        }
    });

    setTimeout(function() {
        error_message = 'Interal Server Error.';
        if (!success) {
            if (options && options.error) {
                options.error(error_message);
            }
            else {
                console.log(error_message);
            }
        }
    }, 30000);
}

       
function handleSubcribeSubmitSuccess(res) {
    
    var obj = JSON.parse(res);
    if(obj.result == "success") {
        jQuery('#mailgun_subscribe_email').val('');
        jQuery('#mailgun_subscribe_thankyoumnsg').html("Thank You for subscribing to the Mailgun blog.  Please check your email account for the verification email.");
    }
    else if (obj.result == "409") {
        jQuery('#mailgun_subscribe_thankyoumnsg').html("This email address is already subscribed to the Mailgun Blog.   <a href='javascript:void(0);' onclick='showSubscribeForm()'>Try another email address</a>.");
    }
    else if (obj.result == "401") {
        jQuery('#mailgun_subscribe_thankyoumnsg').html("Unauthorized access.  We have alerted the authorities.");
    }
} 

function handleSubscribeSubmitError(res) {
    alert("handleSubscribeSubmitError");
}

function showSubscribeForm() {
    jQuery('#mailgun_subscribe_email').val('');
    jQuery('#mailgun_subscribe_box').show();
    jQuery('#mailgun_subscribe_thankyoumnsg').hide();
}

function mgsubscribe_validation_in_progress() {
    
    jQuery('#mailgun_subscribe_email').attr("disabled", true);
    jQuery('#mailgun_subscribe_email').addClass('mgsubcribe_validate_working');
    rdiv = jQuery('#mgsubscribe_validate_status');

    rdiv.empty();
}


function mgsubscribe_validation_success(data) {
    
    jQuery('#mailgun_subscribe_email').removeClass('mgsubcribe_validate_working');
    jQuery('#mailgun_subscribe_email').removeAttr('disabled');
    if (data['is_valid']) {
        jQuery('#mailgun_subscribe_box').hide();
        jQuery('#mailgun_subscribe_thankyoumnsg').html('Submitting ...');
        jQuery('#mailgun_subscribe_thankyoumnsg').show();
        
        var submittedemail = jQuery('#mailgun_subscribe_email').val();
        var jsonData = {
            action: 'mailgun_subscribesubmit', 
            useremail: submittedemail,
            _wpnonce: $MailgunSubscribeVars.nonce_subscribesubmit
        };

        jQuery.ajax({
            'type': 'POST',
            'async': true, 					
            'cache': false,
            'url': $MailgunSubscribeVars.ajaxurl,
            'data': jsonData,
            'success': handleSubcribeSubmitSuccess,
            'error': handleSubscribeSubmitError
        });
    }
    else {
        rdiv = jQuery('#mgsubscribe_validate_status')
    
        // clear out any previous text
        rdiv.empty();

        // set icon and suggestion string
        var errormsg = "Please enter a valid email address <br/>- " + guardpost_link;
        if (data['did_you_mean']) {
            var suggested_email = data['did_you_mean'];
            var suggested_link = "<a href='javascript:mgsubscribe_fill_email(\"" +
                        suggested_email +"\")' class='suggested_email_link'>" +
                        suggested_email + "</a>";
            errormsg = "Did you mean " + suggested_link +"? <br/>- " + guardpost_link;
        }
        rdiv.append(errormsg);
    }
    
}


function mgsubscribe_validation_error(error_message) {
    jQuery('#mailgun_subscribe_email').removeClass('mgsubcribe_validate_working');
    jQuery('#mailgun_subscribe_email').removeAttr('disabled');
    rdiv = jQuery('#mgsubscribe_validate_status')

    // empty out anything we had before
    rdiv.empty()

    // show in progress
    rdiv.append(error_message);
}

function mgsubscribe_fill_email(val) {
    jQuery('#mailgun_subscribe_email').focus().val(val);
}