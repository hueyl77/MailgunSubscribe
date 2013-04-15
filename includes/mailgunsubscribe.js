jQuery().ready(function() {
    jQuery('#mailgun_subscribe_btn').click(function(e) {
        e.preventDefault();
        jQuery('#mailgun_subscribe_box').hide();
        jQuery('#mailgun_subscribe_thankyoumnsg').html('Loading ...');
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

        return false;
    });
});

       
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