<?php
$useremail = $_GET['email'];
$vcode = $_GET['vcode'];
$unsub = $_GET['unsub'];
?>
<div id="subscription_handling_message" style="margin: 10px auto; width: 90%;">Loading ...</div>
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
        jQuery('#subscription_handling_message').html('Thank you for Subscribing to the Mailgun Blog!');
    }
    
    function handleVerifyCodeError(res) {
        
    }
    
    function handleUnsubscribeSuccess(res) {
        jQuery('#subscription_handling_message').html('You have unsubscribed from the Mailgun Blog!');
    } 
    
    function handleUnsubscribeError(res) {
        
    }
</script>