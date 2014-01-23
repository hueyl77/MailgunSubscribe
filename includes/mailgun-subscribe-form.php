<div id="mailgun_subscribe_box">
    <form method="post" action="#">
        <input type="hidden" name="ip" value="127.0.0.1">
        <div class="mailgun_input_container">
            <label for="mailgun_subscribe_email">Your email:</label><br>
            <div class="mailgun_field_wrapper">
                <input type="text" name="mailgun_subscribe_email" id="mailgun_subscribe_email" />
            </div>
            <div id="mgsubscribe_validate_status"></div>
        </div>
        <div class="button_container button_border"><input type="submit" name="subscribe" id="mailgun_subscribe_btn" value="Subscribe"></div>
    </form>
</div>
<div id="mailgun_subscribe_thankyoumnsg" style='display: none;'>
    Please wait ...
</div>