<?php
class MailgunsubscribeTest  {
    function send_publish_post($post) {
        if (!$post) {
            return $post;
        }
$content = $post;
        //$content = str_replace('[gallery', $gallid, $post->post_content);
        if (function_exists('strip_shortcodes')) {
            $content = strip_shortcodes($content);
        }

        // prepare the email message
$post_title = "some post title";
        //$post_title = html_entity_decode($post->post_title, ENT_QUOTES);
        //$permalink = get_permalink($post->ID);
$permalink = "http://somelink";

        $random_hash = substr(md5(uniqid(rand(), true)), 16, 16);
        //$unsubscribe_link = get_site_url() . "/subscription/?email=" . $useremail . "&unsub=1&vcode=" . $random_hash;
$unsubscribe_link = "http://unsublink";

        //$filepath = plugins_url('includes/email-template-newpost.html', __FILE__);
$filepath = 'includes/email-template-newpost.html';
        $htmlmsg = file_get_contents($filepath);

        $htmlmsg = str_replace("%POST_TITLE%", $post_title, $htmlmsg);
        $htmlmsg = str_replace("%PERMALINK%", $permalink, $htmlmsg);
        $htmlmsg = str_replace("%POST_CONTENT%", $content, $htmlmsg);
        $htmlmsg = str_replace("%UNSUBSCRIBE_LINK%", $unsubscribe_link, $htmlmsg);


        // scrub html
        $htmlmsg = $this->scrub_html($htmlmsg);
echo "\n\nHERE2: \n\n";
echo $htmlmsg;
die;
/*
        $from = $this->get_option('from');
        if ($from == "") {
            $from = get_option('admin_email');
        }

        $to = $this->get_option('mailingList');
        $cc = "huey@webmail.us";
        $bcc = "";
        $subject = "[New Post] " . $post_title;
        //$this->send_email($from, $to, $cc, $bcc, $subject, $htmlmsg);
 */
    }
    
    function scrub_html($htmlmsg) {
        $htmlmsg = str_replace("<pre>", "<pre style='background: #efefef; padding: 10px; border: 1px solid #ccc; word-wrap:break-word;'>", $htmlmsg);
        $htmlmsg = $this->resizeImgsInHtml($htmlmsg);
        $htmlmsg = $this->forceNewLineForHtmlImages($htmlmsg);

        return $htmlmsg;
    }

    function resizeImgsInHtml($htmlstr) {
        $MAX_IMG_WIDTH = 400;
        preg_match_all('/<img .+>/', $htmlstr, $matches);

        $imgmatches = $matches[0];
        foreach ($imgmatches as $imgstr) {
            preg_match('/width: .+;/', $imgstr, $width_matches);

            foreach ($width_matches as $widthstr) {
                $widthattr = str_replace('width:', '', $widthstr);
                $widthattr = str_replace('px;', '', $widthattr);

                if (intval($widthattr) > $MAX_IMG_WIDTH) {
                    $widthattr = "width: $MAX_IMG_WIDTH" . "px;";
                    $htmlstr = str_replace($widthstr, $widthattr, $htmlstr);
                }
            }

            preg_match('/width=[\'\"].+[\'\"]/', $imgstr, $width_matches);

            foreach ($width_matches as $widthstr) {
                $widthattr = str_replace('width=', '', $widthstr);
                $widthattr = str_replace('"', '', $widthattr);
                $widthattr = str_replace("'", '', $widthattr);

                if (intval($widthattr) > $MAX_IMG_WIDTH) {
                    $widthattr = "width='$MAX_IMG_WIDTH;'";
                    $htmlstr = str_replace($widthstr, $widthattr, $htmlstr);
                }
            }
        }
        return $htmlstr;
    }
    
    function forceNewLineForHtmlImages($htmlstr) {
        
        $cleardivstr = '<div style="clear: both; margin: 10px;"></div>';
        preg_match_all('/<img .+\/*>/', $htmlstr, $matches);
        $imgmatches = $matches[0];
        foreach($imgmatches as $imgstr) {
            $htmlstr = str_replace($imgstr, $imgstr . $cleardivstr,  $htmlstr);
        }
        return $htmlstr;
    }
}
$filepath = "testhtml.html";
$thepost = file_get_contents($filepath);

$mailgunsubscribe = new MailgunsubscribeTest();
$mailgunsubscribe->send_publish_post($thepost);