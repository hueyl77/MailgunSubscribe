<?php
$useremail = $_GET['email'];
$vcode = $_GET['vcode'];
$unsub = $_GET['unsub'];
?>
<div style="text-align: center;">
<div id="subscription_handling_message" style="margin: 10px auto; width: 90%;"><h1 class="blog_subscribe_message">Loading ...</h1></div>
<div id="link_to_homepage" style="display: none;">
    <!--<div style="clear: both;">Start browsing our recent posts by clicking the button below.</div>-->
    <div style="width: 210px; margin: 15px auto;">
        <div class="button-border">
            <a href="/" class="readmore">Browse Recent Posts</a>
        </div>
    </div>
</div>
<div id="unsubscribed_message" style="display: none;">
    <!--<div style="clear: both;">You will no longer receive up-to-date news from this blog.</div>-->
    <div style="width: 120px; margin: 20px auto;">
        <div class="button-border">
            <a href="/" style="width: 100px;" class="readmore">Okay</a>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    var ajaxurl = '/wp-admin/admin-ajax.php';
    jQuery().ready(function() {
        
        <?php if ($unsub == "1") { ?>
            var jsonData = {
                action: 'mailgun_unsubscribesubmit',
                useremail: '<?php echo $useremail;?>', 
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
        <?php } else { ?>
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
        jQuery('#link_to_homepage').show();
        jQuery('#unsubscribed_message').hide();
    }
    
    function handleVerifyCodeError(res) {
        
    }
    
    function handleUnsubscribeSuccess(res) {
        jQuery('#subscription_handling_message').html('<h1 class="blog_subscribe_message">You have unsubscribed from the Mailgun blog!</h1>');
        jQuery('#link_to_homepage').hide();
        jQuery('#unsubscribed_message').show();
    } 
    
    function handleUnsubscribeError(res) {
        
    }
</script>