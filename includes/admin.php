<?php

class MailgunSubscribeAdmin {

    /**
     * Initialize the default options during plugin activation
     *
     * @return none
     * @since 0.1
     */
    function __construct() {

        $this->options = get_option('mailgunsubscribe');

        $defaults = array(
            'apiKey' => '',
            'apiUrl' => 'https://api.mailgun.net/v2',
            'domain' => '',
            'secure' => '1',
            'from' => '',
            'mailingList' => '',
            'handler_url' => '/subscription',
        );
        if (!$this->options) {
            $this->options = $defaults;
            add_option('mailgunsubscribe', $this->options);
        }

        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('mailgun_subcribe_user', array(&$this, 'subscribe_user'));
        add_action('mailgun_verify_code', array(&$this, 'verify_code'));

        // Activate the options page
        add_action('admin_menu', array(&$this, 'admin_menu'));

        //register ajax actions
        add_action('wp_ajax_mailgun_getmailinglists', array(&$this, 'ajax_get_mailinglists'));
        add_action('wp_ajax_mailgun_createlist', array(&$this, 'ajax_create_mailing_list'));
    }

    /**
     * Add the options page
     *
     * @return none
     * @since 0.1
     */
    function admin_menu() {
        wp_enqueue_script("mailgun_subscribe", plugins_url("mailgunsubscribe.js", __FILE__));
        wp_enqueue_style("mailgun_styles", plugins_url("mailgunsubscribe.css", __FILE__));

        if (current_user_can('manage_options')) {
            $this->hook_suffix = add_options_page(__('Mailgun Subcribe', 'mailgunsubscribe'), __('Mailgun Subscribe', 'mailgunsubscribe'), 'manage_options', 'mailgunsubscribe', array(&$this, 'options_page'));

            add_filter("plugin_action_links_{$this->plugin_basename}", array(&$this, 'filter_plugin_actions'));
            add_action("admin_footer-{$this->hook_suffix}", array(&$this, 'admin_footer_js'));
        }
    }

    /**
     * Output JS to footer for enhanced admin page functionality
     *
     * @since 0.1
     */
    function admin_footer_js() {
        ?>
        <script type="text/javascript">
                                                                                            
            jQuery().ready(function() {
                jQuery('#mailgun_mailinglist_dialog').dialog({
                    closeOnEscape: true,
                    width: 320,
                    height: 160,
                    autoOpen: false,
                    resizable: false
                });
                                
                jQuery('#mailgun_addlist_dialog').dialog({
                    closeOnEscape: true,
                    width: 300,
                    height: 105,
                    resizable: false,
                    autoOpen: false
                });
                                
                jQuery('#mailgun_subscribe_btn').click(function(e) {
                    e.preventDefault();

                    var jsonData = {
                        action: 'mailgun_subscribesubmit',
                        _wpnonce: '<?php echo wp_create_nonce(); ?>'
                    };

                    jQuery.ajax({
                        'type': 'POST',
                        'async': true, 					
                        'cache': false,
                        'url': ajaxurl,
                        'data': jsonData,
                        'success': handleSubcribeSubmitSuccess,
                        'error': handleSubscribeSubmitError
                    });

                    return false;
                });
                    
                jQuery('#mailgun_getmaillist_link').click(function(){
                                    
                    jQuery('#mailgun_mailinglist_wrapper').hide();
                    jQuery('#mailgun_listloading_msg').show();
                                    
                    var posX = jQuery(this).offset().left + 40;
                    var posY = jQuery(this).offset().top;
                    jQuery("#mailgun_mailinglist_dialog").dialog({
                        position:[posX, posY]
                    });
                    jQuery('#mailgun_mailinglist_dialog').dialog('open');
                                                                                            
                    var jsonData = {
                        action: 'mailgun_getmailinglists',
                        _wpnonce: '<?php echo wp_create_nonce(); ?>'
                    };
                                                                                            
                    jQuery.ajax({
                        'type': 'GET',
                        'async': true, 					
                        'cache': false,
                        'url': ajaxurl,
                        'data': jsonData,
                        'datatype': 'json',
                        'success': handleGetMailinglistsSuccess,
                        'error': handleGetMailinglistsError
                    });
                });
                                
                jQuery('#mailgun_createlist_link').click(function(){
                    var posX = jQuery(this).offset().left + 40;
                    var posY = jQuery(this).offset().top;
                    jQuery("#mailgun_addlist_dialog").dialog({
                        position:[posX, posY]
                    });
                    jQuery('#mailgun_addlist_dialog').dialog('open');
                });
            });// document.ready
                    
            function mailgun_AddMailinglist() {
                var newaddress = jQuery('#mailgun_newmailinglist').val();
                jQuery('#mailgun_createlist_wrapper').hide();
                jQuery('#mailgun_addinglist_msg').show();
                        
                var jsonData = {
                    action: 'mailgun_createlist',
                    _wpnonce: '<?php echo wp_create_nonce(); ?>',
                    address: newaddress
                };
                                                                                            
                jQuery.ajax({
                    'type': 'POST',
                    'async': true, 					
                    'cache': false,
                    'url': ajaxurl,
                    'data': jsonData,
                    'datatype': 'json',
                    'success': handleCreateListSuccess,
                    'error': handleCreateListError
                });
            }
            /* ]]> */
        </script>
        <?php
    }

    /**
     * Output the options page
     *
     * @return none
     * @since 0.1
     */
    function options_page() {
        if (!@include( 'options-page.php' )) {
            printf(__('<div id="message" class="updated fade"><p>The options page for the <strong>Mailgun Subscribe</strong> plugin cannot be displayed. The file <strong>%s</strong> is missing.  Please reinstall the plugin.</p></div>', 'mailgunsubscribe'), dirname(__FILE__) . '/options-page.php');
        }
    }

    /**
     * Wrapper function hooked into admin_init to register settings
     * and potentially register an admin notice if the plugin hasn't
     * been configured yet
     *
     * @return none
     * @since 0.1
     */
    function admin_init() {
        $this->register_settings();
        $apiKey = $this->get_option('apiKey');
        $domain = $this->get_option('domain');

        if (empty($apiKey) || empty($domain)) {
            add_action('admin_notices', array(&$this, 'admin_notices'));
        }
    }

    /**
     * Whitelist the mailgun options
     *
     * @since 0.1
     * @return none
     */
    function register_settings() {
        register_setting('mailgunsubscribe', 'mailgunsubscribe', array(&$this, 'validation'));
    }

    /**
     * Data validation callback function for options
     *
     * @param array $options An array of options posted from the options page
     * @return array
     * @since 0.1
     */
    function validation($options) {

        $apiKey = trim($options['apiKey']);
        if (!empty($apiKey)) {
            $pos = strpos($apiKey, 'key-');
            if ($pos === false || $pos > 4)
                $apiKey = "key-{$apiKey}";

            $pos = strpos($apiKey, 'api:');
            if ($pos !== false && $pos == 0)
                $apiKey = substr($apiKey, 4);
            $options['apiKey'] = $apiKey;
        }

        foreach ($options as $key => $value)
            $options[$key] = trim($value);

        $this->options = $options;
        return $options;
    }

    /**
     * Function to output an admin notice when the plugin has not
     * been configured yet
     *
     * @return none
     * @since 0.1
     */
    function admin_notices() {
        $screen = get_current_screen();
        if ($screen->id == $this->hook_suffix)
            return;
        ?>
        <div id='mailgunsubscribe-warning' class='updated fade'><p><strong><?php _e('The Mailgun Subscribe widget is almost ready. ', 'mailgunsubscribe'); ?></strong><?php printf(__('You must <a href="%1$s">configure Mailgun</a> for it to work.', 'mailgunsubscribe'), menu_page_url('mailgunsubscribe', false)); ?></p></div>
        <?php
    }

    /**
     * Add a settings link to the plugin actions
     *
     * @param array $links Array of the plugin action links
     * @return array
     * @since 0.1
     */
    function filter_plugin_actions($links) {
        $settings_link = '<a href="' . menu_page_url('mailgunsubscribe', false) . '">' . __('Settings', 'mailgunsubscribe') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Setup AJAX call handlers, set content-type, headers, check nonce
     *
     * @since 0.1
     */
    function ajax_callback_setup() {

        nocache_headers();
        header('Content-Type: application/json');

        if (!wp_verify_nonce($_GET['_wpnonce'])) {
            die(json_encode(array(
                        'message' => __('Unauthorized', 'mailgunsubscribe'),
                        'method' => null))
            );
        }
    }

    /**
     * AJAX callback function to get mailing lists
     *
     * @return json string
     * @since 0.1
     */
    function ajax_get_mailinglists() {
        $this->ajax_callback_setup();

        $apiUrl = $this->get_option('apiUrl');
        $apiKey = $this->get_option('apiKey');
        $apiAuthCred = "api:" . $apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiAuthCred);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/lists');

        $errormsg = "";
        if (!$result = curl_exec($ch)) {
            $errormsg = curl_error($ch);
            die(
                    json_encode(array(
                        'error' => print_r($errormsg, true))
                    )
            );
        }
        $info = curl_getinfo($ch);
        curl_close($ch);

        die(
                json_encode(array(
                    'apiAuthCred' => $apiAuthCred,
                    'info' => print_r($info, true),
                    'statuscode' => $info['httpcode'],
                    'result' => print_r($result, true))
                )
        );
    }

    /**
     * AJAX callback function to create mailing list
     *
     * @return string
     * @since 0.1
     */
    function ajax_create_mailing_list() {

        $apiKey = $this->get_option('apiKey');
        $apiUrl = $this->get_option('apiUrl');
        $apiAuthCred = "api:" . $apiKey;
        $newaddress = $_POST['address'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiAuthCred);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/lists');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('address' => $newaddress,
            'description' => 'Added by Mailgun Wordpress Widget'));

        $errormsg = "";
        if (!$result = curl_exec($ch)) {
            $errormsg = curl_error($ch);
        }
        //$info = curl_getinfo($ch);
        curl_close($ch);

        die(
                json_encode(array(
                    'result' => print_r($result, true),
                    'error' => print_r($errormsg, true))
                )
        );
    }

    /**
     * Get specific option from the options table
     *
     * @param string $option Name of option to be used as array key for retrieving the specific value
     * @return mixed
     * @since 0.1
     */
    function get_option($option, $options = null) {
        if (is_null($options))
            $options = &$this->options;
        if (isset($options[$option]))
            return $options[$option];
        else
            return false;
    }
}