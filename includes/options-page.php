<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
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
                    <p class="description"><?php _e('The mailing list to add subscribers to.  You can manage mailing lists from the Mailgun <a href="https://mailgun.com/cp" target="_blank">control panel</a>', 'mailgunsubscribe'); ?></p>
                </td>
            </tr>
            <tr valign="top" class="mailgun-api">
                <th scope="row">
                    <?php _e('From Email', 'mailgunsubscribe'); ?>
                </th>
                <td>
                    <input type="text" id="mailgun_settings_from" class="regular-text" name="mailgunsubscribe[from]" value="<?php esc_attr_e($this->get_option('from')); ?>" placeholder="Mailgun Blog <postmaster@blog.mailgun.com>" />
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