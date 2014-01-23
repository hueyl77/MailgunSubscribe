<?php
$useremail = $_GET['email'];
$unsubemail = $_GET['u'];
$vcode = $_GET['vcode'];
$unsub = $_GET['unsub'];
?>
<style type="text/css">
    
    #unsubform {
        max-width: 560px;
        margin: 0 auto;
    }
    
    #unsubemail {
        margin-right: 10px;
        height: 17px;
        width: 230px;
        padding: 7px;
        font-size: 20px;
        color: #666;
    }
    
    .unsubemail_label {
        font-size: 18px; 
        color: #666;
        font-weight: normal;
    }
</style>

<div style="text-align: center;">
    <div id="subscription_handling_message" style="margin: 10px auto; width: 90%; display: none;"><h1 class="blog_subscribe_message">Loading ...</h1></div>
<?php if ($unsub == "1") { ?>
<div id="unsubform">
    <label class="unsubemail_label" for="unsubemail">We're sorry to see you go. Just confirm that you no longer want to receive blog updates.</label><br/>
    <div style="margin: 25px auto; width: 150px;text-align: center;">
        <div style="float: left;">
            <!--
            <input id="unsubemail" value="<?php echo $unsubemail; ?>" onfocus="emailFieldFocus(this)" onblur="emailFieldBlur(this)" disabled="disabled" />
            -->
        </div>
        <div style="float: left;"><input type="button" value="Unsubscribe" onclick="unsubscribe()" style="height: 35px; padding-top: 8px;" /></div>
    </div>
</div>
<?php } else { ?>
    <div id="link_to_homepage" style="display: none;">
        <!--<div style="clear: both;">Start browsing our recent posts by clicking the button below.</div>-->
        <div style="width: 210px; margin: 15px auto;">
            <div class="button-border">
                <a href="/" class="readmore">Browse Recent Posts</a>
            </div>
        </div>
    </div>
<?php } ?>

<div id="unsubscribed_message" style="display: none;">
    <!--<div style="clear: both;">You will no longer receive up-to-date news from this blog.</div>-->
    <div style="width: 210px; margin: 15px auto;">
        <div class="button-border">
            <a href="/" class="readmore">Browse Recent Posts</a>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    var ajaxurl = '/wp-admin/admin-ajax.php';
    jQuery().ready(function() {
        
        <?php if ($unsub == "1") { ?>
            jQuery('#subscription_handling_message').hide();
        <?php } else { ?>
            jQuery('#subscription_handling_message').show();
            var jsonData = {
                action: 'mailgun_handle_vlink',
                useremail: '<?php echo $useremail;?>', 
                vcode: '<?php echo $vcode;?>' 
            };
            jQuery.ajax({
                'type': 'POST',
                'async': true, 					
                'cache': false,
                'url': ajaxurl,
                'data': jsonData,
                'success': handleVerifyCodeSuccess,
                'error': handleVerifyCodeError
            });
        <?php } ?>
 
    });
    
    function handleVerifyCodeSuccess(res) {
        jQuery('#subscription_handling_message').html('<h1 class="blog_subscribe_message">Thank you for subscribing to the Mailgun blog!</h1>.');
        jQuery('#subscription_handling_message').show();
        jQuery('#link_to_homepage').show();
        jQuery('#unsubscribed_message').hide();
    }
    
    function handleVerifyCodeError(res) {
        
    }
    
    function handleUnsubscribeSuccess(res) {
        jQuery('#subscription_handling_message').html('<label class="unsubemail_label">You have unsubscribed from the Mailgun blog!</label>');
        jQuery('#subscription_handling_message').show();
        jQuery('#link_to_homepage').hide();
        jQuery('#unsubscribed_message').show();
        jQuery('#unsubform').hide();
    } 
    
    function handleUnsubscribeError(res) {
        
    }
    
    function emailFieldFocus(field) {
        if (field.value.toLowerCase() == "email address") {
            field.value = "";
        }
    }
    
    function emailFieldBlur(field) {
        if (jQuery.trim(field.value) == "") {
            field.value = "Email Address";
        }
    }
    
    function unsubscribe() {
                
        //var unsubemail = jQuery('#unsubemail').val();
        var unsubemail = "<?php echo $unsubemail;?>";
        if (jQuery.trim(unsubemail).length == 0 || jQuery.trim(unsubemail).toLowerCase() == "email address") {
            alert("Please enter an email address");
            return;
        }

        var jsonData = {
            action: 'mailgun_unsubscribesubmit',
            useremail: unsubemail, 
            vcode: '<?php echo $vcode;?>' 
        };
        jQuery.ajax({
            'type': 'POST',
            'async': true, 					
            'cache': false,
            'url': ajaxurl,
            'data': jsonData,
            'success': handleUnsubscribeSuccess,
            'error': handleUnsubscribeError
        });
    }
</script>