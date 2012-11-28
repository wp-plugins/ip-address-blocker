<?php
global $wpdb, $show_blank_page_to_banned_user, $show_lionscripts_attribution; 
define('WIB_BASIC_WEB_HOME_PAGE_NAME_SHORT', 'LionScripts');
define('WIB_BASIC_FULL_NAME', 'IP Address Blocker');
define('WIB_BASIC_WEB_HOME_PAGE_NAME', WIB_BASIC_WEB_HOME_PAGE_NAME_SHORT . '.com');
define('WIB_BASIC_FULL_HOME_PAGE_LINK', 'http://www.' . strtolower(WIB_BASIC_WEB_HOME_PAGE_NAME) . '/');
define('WIB_BASIC_URL_NAME', str_replace(' ', '-', strtolower(WIB_BASIC_FULL_NAME)));
define('WIB_BASIC_FULL_PAGE_LINK', strtolower(WIB_BASIC_FULL_HOME_PAGE_LINK) . WIB_BASIC_URL_NAME);
define('WIB_BASIC_FULL_NAME_PRO', WIB_BASIC_FULL_NAME . ' Pro');
define('WIB_BASIC_FULL_NAME_HEADING', 'LionScripts: ' . WIB_BASIC_FULL_NAME . ' Basic');
define('WIB_BASIC_SHORT_NAME', WIB_BASIC_FULL_NAME);
define('WIB_BASIC_TABLE_NAME', strtolower(WIB_BASIC_WEB_HOME_PAGE_NAME_SHORT) . '_' . str_replace(' ', '_', strtolower(WIB_BASIC_FULL_NAME)));
define('WIB_BASIC_TABLE_NAME_OPTIONS', WIB_BASIC_TABLE_NAME . "_options");
define('WIB_BASIC_BASE', get_bloginfo('wpurl')."/wp-content/plugins/".basename(dirname(__FILE__)));
define('WIB_ADMIN_URL', get_admin_url()."admin.php?page=".basename(dirname(__FILE__)));
define('WIB_BASIC_ASSETS', WIB_BASIC_BASE . '/assets');
define('WIB_BASIC_DB_VERSION', '1.1');
define('WIB_BASIC_SUPPORT_EMAIL', 'support@' . strtolower(WIB_BASIC_WEB_HOME_PAGE_NAME));
define('WIB_BASIC_FACEBOOK_LINK', "http://www.facebook.com/" . WIB_BASIC_WEB_HOME_PAGE_NAME_SHORT);
define('WIB_BASIC_TWITTER_LINK', 'http://twitter.com/' . WIB_BASIC_WEB_HOME_PAGE_NAME_SHORT);
define('WIB_BASIC_CURRENT_USER_IP', $_SERVER['REMOTE_ADDR']);
define('WIB_BASIC_ATTRIB', '<font style="font-size:12px;"><center>IP Blocking Protection is enabled by <a href="' . WIB_BASIC_FULL_PAGE_LINK . '" target="_blank">' . WIB_BASIC_FULL_NAME . '</a> from <a href="' . WIB_BASIC_FULL_HOME_PAGE_LINK . '" target="_blank">' . WIB_BASIC_WEB_HOME_PAGE_NAME . '</a>.</center></font>');
?>