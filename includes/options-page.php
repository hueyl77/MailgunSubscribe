<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <span class="alignright" style="margin: 10px;"><a target="_blank" href="http://www.mailgun.com/"><img src="https://mailgun.net/static/img/logo.png" alt="Mailgun" /></a></span>
    <h2><?php _e('Mailgun Subscribe', 'mailgunsubscribe'); ?></h2>
    <p>A <a target="_blank" href="http://www.mailgun.com/">Mailgun</a> account is required to use this plugin and the Mailgun service.</p>
    <p>If you need to register for an account, you can do so at <a target="_blank" href="http://www.mailgun.com/">http://www.mailgun.com/</a>.</p>
    <form id="mailgunsubscribe-form" action="options.php" method="post">
        <?php settings_fields('mailgunsubscribe'); ?>
        <h3><?php _e('Configuration', 'mailgunsubscribe'); ?></h3>
        <table class="form-table">
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('API URL', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" class="regular-text" name="mailgunsubscribe[apiUrl]" value="<?php esc_attr_e($this->get_option('apiUrl')); ?>" placeholder="https://api.mailgun.net/v2" />
                    <p class="description"><?php _e('Your Mailgun API Url.  You can find your Mailgun API Url in your Mailgun control panel.', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Mailgun Domain Name', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" class="regular-text" name="mailgunsubscribe[domain]" value="<?php esc_attr_e($this->get_option('domain')); ?>" placeholder="samples.mailgun.org" />
                    <p class="description"><?php _e('Your Mailgun Domain Name.', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('API Key', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" class="regular-text" name="mailgunsubscribe[apiKey]" value="<?php esc_attr_e($this->get_option('apiKey')); ?>" placeholder="key-3ax6xnjp29jd6fds4gc373sgvjxteol0" />
                    <p class="description"><?php _e('Your Mailgun API key, that starts with and includes "key-". Only valid for use with the API.', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('Mailing List', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" id="mailgun_settings_mailinglist" class="regular-text" name="mailgunsubscribe[mailingList]" value="<?php esc_attr_e($this->get_option('mailingList')); ?>" placeholder="example-list@mailgun.org" />
                    <a id="mailgun_getmaillist_link" href="#">[select existing lists]</a> 
                    <a id="mailgun_createlist_link" href="#">[create new]</a>
                    <p class="description"><?php _e('The mailing list to add subscribers to.  You can manage mailing lists from the Mailgun <a href="https://mailgun.net/cp" target="_blank">control panel</a>', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('From Email', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" id="mailgun_settings_from" class="regular-text" name="mailgunsubscribe[from]" value="<?php esc_attr_e($this->get_option('from')); ?>" placeholder="Mailgun Blog <postmaster@blog.mailgun.net>" />
                    <p class="description"><?php _e('The From address of emails sent by this plugin.  Must be a valid email address.', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('Handler Url', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" id="mailgun_settings_handler_url" class="regular-text" name="mailgunsubscribe[handler_url]" value="<?php esc_attr_e($this->get_option('handler_url')); ?>" placeholder="/Subscription" />
                    <p class="description"><?php _e('Url to the handler page the link in the verification email will point to', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'mailgunsubscribe'); ?>" />
        </p>
    </form>
</div>

<div id="mailgun_mailinglist_dialog" style="display: none;">
    <div id="mailgun_listloading_msg" class="loading"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" alt="" /> <?php _e('Loading…', 'related-links'); ?></div>
    <div id="mailgun_mailinglist_wrapper" style="display: none;"></div>
    <div style="text-align: center; margin-top: 10x;">[<a href="javascript:void(0)" onclick="mailgun_closeDialog('mailgun_mailinglist_dialog')">close</a>]</div>
</div>

<div id="mailgun_addlist_dialog" style="display: none;">
    <div id="mailgun_addinglist_msg" style="display: none; margin: 10px;" class="loading"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" alt="" /> <?php _e('Adding…', 'related-links'); ?></div>
    <div id="mailgun_createlist_wrapper" style="margin: 10px;">
        Enter an address:<br/>
        <input id="mailgun_newmailinglist" type="text" class="regular-text" name="mailgunsubscribe[newlist]" placeholder="newlist@example.mailgun.org" style="width: 215px;" />
        <input type="button" value="Create" onclick="mailgun_AddMailinglist()" />
    </div>
    <div style="text-align: center; margin-top: 10x;">
        [<a href="javascript:void(0)" onclick="mailgun_closeDialog('mailgun_addlist_dialog')">close</a>]
    </div>
</div>