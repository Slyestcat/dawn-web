<?php

// the name of your website. used around the website
define('site_title', 'Dawn');

// the folder where this script is located. if in root, should just be / 
// most likely will be /vote/
define('web_root', 'vote/');

// SQL Information. use the information provided in step 1.
define('MYSQL_HOST', '68.178.222.132'); # usually localhost
define('MYSQL_DATABASE', 'dawn_vote');
define('MYSQL_USERNAME', 'dawn_reader');
define('MYSQL_PASSWORD', '+y6$h81T5R[[');

// enable or disable the user above. Should only
// be used as a last resort.
define("disable_root", false);

// ROOT USER ACCESS. disabled by default. Enable by setting disable_root to false
define('admin_username', 'dawn_admin');
define('admin_password', 'd_admin00000000001!~');


// show/hide the admin button
define("show_admin", 1);

// enables or disables dark mode
define("dark_mode", 1);

// CHANGE THIS
define("api_key", "dfsdfsdfsdfds");

DEFINE("players_online", file_get_contents( "../resources/players.txt"));
# OPTIONS CREATED BY INSTALLER





