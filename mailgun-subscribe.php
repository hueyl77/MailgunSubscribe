<?php
/*
  Plugin Name: MailgunSubscribe
  Plugin URI: http://mailgun.com/
  Description: Allows users to subscribe to your blog by email through the Mailgun API
  Author: Huey Ly
  Version: 1.0
  Author URI: http://mailgun.com
 */

class Mailgunsubscribe extends WP_Widget {

    var $_excerpt_words_length = 70; // number of words to use from post if no excerpt
    var $_submit_allowed_interval = 10; // number of seconds allowed between submissions
    var $_vcode_hashkey = "MailgunROCKS2013";
    var $_vcode_algo = "ripemd160";

    function Mailgunsubscribe() {
        $widget_ops = array('classname' => 'Mailgunsubscribe', 'description' => 'Allows your user to subscribe to your blog by email using the Mailgun API');
        $this->WP_Widget('Mailgunsubscribe', 'Mailgun Subscribe', $widget_ops);

        $this->options = get_option('mailgunsubscribe');
        $this->plugin_file = __FILE__;
        $this->plugin_basename = plugin_basename($this->plugin_file);

        // shortcodes
        add_shortcode('mailgun-subscribe-handler', array(&$this, 'show_subscribe_handler_page'));

        // ajax action hooks
        add_action('wp_ajax_nopriv_mailgun_subscribesubmit', array(&$this, 'ajax_subscribe_form_submit'));
        add_action('wp_ajax_nopriv_mailgun_unsubscribesubmit', array(&$this, 'ajax_unsubscribe_form_submit'));
        add_action('wp_ajax_nopriv_mailgun_handle_vlink', array(&$this, 'ajax_handle_verification_link'));

        add_action('wp_ajax_mailgun_subscribesubmit', array(&$this, 'ajax_subscribe_form_submit'));
        add_action('wp_ajax_mailgun_unsubscribesubmit', array(&$this, 'ajax_unsubscribe_form_submit'));
        add_action('wp_ajax_mailgun_handle_vlink', array(&$this, 'ajax_handle_verification_link'));
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = $instance['title'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        return $instance;
    }

    function widget($args, $instance) {
        extract($args, EXTR_SKIP);

        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        if (!empty($title))
            echo $before_title . $title . $after_title;;

        include ('includes/mailgun-subscribe-form.php');


        echo $after_widget;
    }

    function show_subscribe_handler_page() {
        include ('includes/mailgun-subcribe-handler.php');
    }

    /**
     * Checks if the last submission was less than a certain time
     *
     * @since 0.1
     */
    function check_submitted_dt_diff() {
        session_start();
        $lastDtSubmitted = $_SESSION['subscribe_lastdt_submitted'];

        if ($lastDtSubmitted == "") {
            $_SESSION['subscribe_lastdt_submitted'] = date_format(new DateTime(), 'Y-m-d H:i:s');
            return true;
        }
        $lastDtSubmitted = new DateTime($lastDtSubmitted);
        $now = new DateTime("now");

        // prevent quick submission
        $secondsPassed = $now->getTimestamp() - $lastDtSubmitted->getTimestamp();

        $_SESSION['subscribe_lastdt_submitted'] = date_format($now, 'Y-m-d H:i:s');
        if ($secondsPassed < $this->_submit_allowed_interval) {
            die(json_encode(array(
                        'result' => "Failed.  Please wait a bit to submit again.")
                    ));
        }

        return true;
    }

    /**
     * AJAX callback handles subscribe form submission
     *
     * @return string
     * @since 0.1
     */
    function ajax_subscribe_form_submit() {

        // prevent quick submissions
        $this->check_submitted_dt_diff();

        //get submitted email address
        $useremail = $_POST['useremail'];

        // generate hashcode link
        $random_hash = hash_hmac($this->_vcode_algo, $useremail, $this->_vcode_hashkey);
        $vlink = get_site_url() . "/subscription/?email=" . $useremail . "&vcode=" . $random_hash;

        // sends opt-in email to user with hashcode link
        $filepath = plugins_url('includes/email-template-verification.html', __FILE__);
        $htmlmsg = file_get_contents($filepath);
        $htmlmsg = str_replace("%verification_hander_link%", $vlink, $htmlmsg);

        if (function_exists('strip_shortcodes')) {
            $htmlmsg = strip_shortcodes($htmlmsg);
        }

        $filepath = plugins_url('includes/email-template-verification.txt', __FILE__);
        $plaintextmsg = file_get_contents($filepath);
        $plaintextmsg = str_replace("%verification_hander_link%", $vlink, $plaintextmsg);

        $from = $this->get_option('from');
        if ($from == "") {
            $from = get_option('admin_email');
        }
        $to = $useremail;
        $cc = "";
        $bcc = "";
        $subject = "Please verify your Mailgun blog subscription";

        $this->send_email($from, $to, $cc, $bcc, $subject, $plaintextmsg, $htmlmsg);
        die(
                json_encode(array(
                    'result' => "success")
                )
        );
    }

    /**
     * AJAX callback handles unsubscribe submission
     *
     * @return string
     * @since 0.1
     */
    function ajax_unsubscribe_form_submit() {

        // prevent quick submissions
        $this->check_submitted_dt_diff();

        $useremail = $_POST['useremail'];
        $mailgunDomain = $this->get_option('domain');
        $mailinglist = $this->get_option('mailingList');

        $apiKey = $this->get_option('apiKey');
        $apiUrl = $this->get_option('apiUrl');
        $apiAuthCred = "api:" . $apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiAuthCred);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt(
                $ch, CURLOPT_URL, $apiUrl . '/lists/' . $mailinglist . '/members/' . $useremail);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('subscribed' => false));

        $errormsg = "";
        if (!$result = curl_exec($ch)) {
            $errormsg = curl_error($ch);
            die(json_encode(array('result' => "error: " . $errormsg)));
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        die(json_encode(array(
                    'result' => "success",
                    'info' => print_r($info, true))
        ));
    }

    /**
     * Call Mailgun API to add member to mailinglist
     *
     * @return string
     * @since 0.1
     */
    function subscribe_user($useremail) {

        // add member to mailing list
        $mailinglist = $this->get_option('mailingList');
        $apiKey = $this->get_option('apiKey');
        $apiUrl = $this->get_option('apiUrl');
        $apiAuthCred = "api:" . $apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiAuthCred);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/lists/' . $mailinglist . '/members');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('address' => $useremail,
            'subscribed' => true));

        $errormsg = "";
        if (!$result = curl_exec($ch)) {
            $errormsg = curl_error($ch);
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
    }

    /**
     * Verify haschode and add user to mailing list
     *
     * @return string
     * @since 0.1
     */
    function ajax_handle_verification_link() {

        // prevent quick submissions
        $this->check_submitted_dt_diff();

        $useremail = $_POST['useremail'];
        $vcode = $_POST['vcode'];

        // verify code against email
        $email_hmachash = hash_hmac($this->_vcode_algo, $useremail, $this->_vcode_hashkey);
        $hashmatched = hash_compare($email_hmachash, $vcode);
        if (!$hashmatched) {
            die(
                    json_encode(array(
                        'result' => 'error: vcode invalid')
                    )
            );
        }

        // add email to mailing list
        $this->subscribe_user($useremail);

        die(
                json_encode(array(
                    'result' => 'success')
                )
        );
    }
    
    function get_subscribed_user($useremail) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-3ax6xnjp29jd6fds4gc373sgvjxteol0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v2/lists/' . $useremail);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
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

    function send_email($from, $to, $cc="", $bcc="", $subject, $plaintextmsg, $htmlmsg) {
        $mailgunDomain = $this->get_option('domain');
        $apiKey = $this->get_option('apiKey');
        $apiUrl = $this->get_option('apiUrl');
        $apiAuthCred = "api:" . $apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $apiAuthCred);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/' . $mailgunDomain . '/messages');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('from' => $from,
            'to' => $to,
            'subject' => $subject,
            'text' => $plaintextmsg,
            'html' => $htmlmsg));

        $errormsg = "";
        if (!$result = curl_exec($ch)) {
            $errormsg = curl_error($ch);
            die(
                    json_encode(array(
                        'error' => print_r($errormsg, true))
                    )
            );
        }

        //$info = curl_getinfo($ch);

        curl_close($ch);
        return $result;
    }

}

function mailgun_subscribe_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script("mailgun_subscribe", plugins_url("includes/mailgunsubscribe.js", __FILE__));
    wp_enqueue_style("mailgun_styles", plugins_url("includes/mailgunsubscribe.css", __FILE__));
}

function hash_compare($a, $b) {
    if (!is_string($a) || !is_string($b)) {
        return false;
    }
    $lena = strlen($a);
    $lenb = strlen($b);
    if ($lena !== $lenb) {
        return false;
    }
    $match = true;
    for ($i = 0; $i < $lena; $i++) {
        $match = $match && ((ord($a[$i]) ^ ord($b[$i])) === 0);
    }
    return $match;
}

// action hooks
add_action('wp_enqueue_scripts', 'mailgun_subscribe_scripts');
add_action('widgets_init', create_function('', 'return register_widget("Mailgunsubscribe");'));

if (is_admin()) {
    if (@include( dirname(__FILE__) . '/includes/admin.php' )) {
        $mailgunsubscribeAdmin = new MailgunSubscribeAdmin();
    }
}
