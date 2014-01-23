=== Plugin Name ===
Contributors: hueyl77 (this should be a list of wordpress.org userid's)
Donate link: http://hueyly.com/
Tags: Email, Subscribe, Subscription
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows site visitors to subscribe to a blog via email to receive notifications of
new blog posts.

== Description ==

Visitors to a blog site can enter an email address in a widget form to subscribe to the blog.
Subscribers of the blog will receive notifications via email (sent by the Mailgun 
server) whenever a new blog is posted.  Opt-in email built in and is required.  
Needs at least a free Mailgun account to work.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload all content of the `mailgun-subscribe` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress admin dashboard
1. Place the form on your theme through the Widgets menu in the Wordpress admin dashboard
1. Create a page call and add the shortcode [mailgun-subscribe-handler].  This is the landing page
for users when the click the link in the opt-in email
1. In the Wordpress admin dashboard, go to Settings -> Mailgun Subscribe and enter
your Mailgun credentials.  Enter the page url from the above step as the Hnadler Url.  Save Changes.
1. You can tweak the look of your form by modifying 
mailgun-subscribe/includes/mailgun-subscribe-form.php and mailgunsubscribe.css
1. You can change the opt-in email by modifying mailgun-subscribe/includes/email-template-verification.txt
and email-template-verification.html
1. You can change the new posts notification email by modifying mailgun-subscribe/includes/email-template-newpost.txt
and email-template-newpost.html