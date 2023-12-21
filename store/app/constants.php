<?php
/**
 * Your mission, should you choose to accept it, is to configure
 * this without breaking on the first try. This message will
 * self destruct after 10 seconds. (not rly)
 */

/**
 * Shows up in a few places, like the footer, 
 * and in the card above the products.
 */
define('site_title', 'Dawn');

DEFINE("players_online", file_get_contents("../resources/players.txt"));

/**
 * The path of this script. Likely /store/
 */
define('web_root', '/store/');

define('FORUM_URL', '/forum/');

/**
 * Database details.
 */
define('MYSQL_HOST', '68.178.222.132');
define('MYSQL_DATABASE', 'dawn_store');
define('MYSQL_USERNAME', 'dawn_reader');
define('MYSQL_PASSWORD', '+y6$h81T5R[[');

/**
 * Shows or hides the button to the admin panel in the top-right.
 * if you're paranoid, set it to false.
 */
define("show_admin_link", true);

/**
 * Admin login username and password.
 * Pretty imperative that you do NOT share this with anyone or ninjas will attack you.
 * DO NOT LEAVE THIS DEFAULT!!!!!!!!!
 */
define("admin_username", "primal_admin");
define("admin_password", "p_admin000000001asdas!~");

/**
 * Paypal Options. Setting DEBUG to 0 will disable the log file. good
 * to enable if you think you're not receiving callback from paypal.
 */
define("DEBUG", 1);
define("LOG_FILE", "paypal.log");

/**
 * Enables or disables the use of sandbox. Should be 0 for live use.
 */
define("USE_SANDBOX", 1);

/**
 * Edit business, return, cancel_return, and notify_url
 */
const pp_config = array(
    'business' 			=> "primalosps@gmail.com",
    'no_note' 			=> 1,
    'cmd'				=> "_cart",
    'upload'			=> 1,
    'address_override' 	=> 1,
    'return' 			=> "https://primalps.net/store/",
    'cancel_return' 	=> "https://primalps.net/store/",
    'notify_url' 		=> "https://primalps.net/store/ipn",
    'cpp_header_image' 	=> "https://primalps.net/resources/images/Logo.png"
);

/**
 * Stripe options. Set deubg to 0 will disable the log file. 
 * To enable it, to check for callbacks set to 1.
 */

DEFINE('STRIPE_DEBUG', 1);
DEFINE('STRIPE_LOG', "stripe.log");

/**
 * Define stripe API secret key.
**/
DEFINE('SECTRET_KEY', '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');
DEFINE('ENDPOINT_KEY', 'whsec_6FdfrUCl8sF5XZf1ZNHLjL8Tk7aiTFDK');
/**
 * Enables or disables the user of stripe developer test mode. 0 for live mode,
 * 1 for testing purposes
 */

DEFINE('STRIPE_DEV', 1);
