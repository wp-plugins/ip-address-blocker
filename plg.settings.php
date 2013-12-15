<?php

global $LIONSCRIPTS;
global $objLionTemp;

if(!defined('LIONSCRIPTS_SITE_NAME_SHORT'))
	define('LIONSCRIPTS_SITE_NAME_SHORT', 'LionScripts');

if(!defined('LIONSCRIPTS_SITE_NAME'))
	define('LIONSCRIPTS_SITE_NAME', LIONSCRIPTS_SITE_NAME_SHORT.'.com');

if(!defined('LIONSCRIPTS_HOME_PAGE_URL'))
	define('LIONSCRIPTS_HOME_PAGE_URL', 'http://www.'.strtolower(LIONSCRIPTS_SITE_NAME).'/');

if(!defined('LIONSCRIPTS_SUPPORT_PAGE_URL'))
	define('LIONSCRIPTS_SUPPORT_PAGE_URL', 'http://support.'.strtolower(LIONSCRIPTS_SITE_NAME).'/');

if(!defined('LIONSCRIPTS_FACEBOOK_LINK'))
	define('LIONSCRIPTS_FACEBOOK_LINK', "http://www.facebook.com/".LIONSCRIPTS_SITE_NAME_SHORT);

if(!defined('LIONSCRIPTS_TWITTER_LINK'))
	define('LIONSCRIPTS_TWITTER_LINK', 'http://twitter.com/'.LIONSCRIPTS_SITE_NAME_SHORT);

if(!defined('LIONSCRIPTS_GOOGLE_PLUS_LINK'))
	define('LIONSCRIPTS_GOOGLE_PLUS_LINK', 'http://plus.google.com/+'.LIONSCRIPTS_SITE_NAME_SHORT);

if(!defined('LIONSCRIPTS_YOUTUBE_LINK'))
	define('LIONSCRIPTS_YOUTUBE_LINK', 'http://www.youtube.com/user/'.LIONSCRIPTS_SITE_NAME_SHORT);

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lionscripts_plg.class.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'lionscripts_plg_wib.class.php');

$objLionTemp = new lionscripts_plg_wib(basename(dirname(__FILE__)));
$LIONSCRIPTS[$objLionTemp->plg_identifier]['OBJ'] = $objLionTemp;

$LIONSCRIPTS['WP_PRODUCTS'][$objLionTemp->plg_hook_version] = array(
																		'WIB'=>array(
																					'name'=>'IP Address Blocker', 
																					'wp_url_var'=>'ip-address-blocker',
																					'url'=>LIONSCRIPTS_HOME_PAGE_URL.'product/wordpress-ip-address-blocker-pro/'
																				),
																		'MNN'=>array(
																					'name'=>'Site Maintenance and Noindex-Nofollow', 
																					'wp_url_var'=>'maintenance-and-noindex-nofollow',
																					'url'=>LIONSCRIPTS_HOME_PAGE_URL.'product/maintenance-and-noindex-nofollow/'
																				)																	);

$LIONSCRIPTS['ABOUT_US'][$objLionTemp->plg_hook_version] = '<p>
																<a href="'.LIONSCRIPTS_HOME_PAGE_URL.'" target="_blank"><img src="'.$objLionTemp->plg_images['www'].'logo.png" class="left ls_logo_about" /></a>
																'.LIONSCRIPTS_SITE_NAME.' is an organization with mission to extend WordPress Possibilities so that every person can get maximum benefit by using Wordpress as their site\'s CMS platform, no matter whether he/she is Novice or Professional.
															</p>
															<p>
																You can spread our mission by sharing and following us on the social sites.
															</p>
															<p>
																<ul class="socialicons color">
																	<li><a href="'.LIONSCRIPTS_FACEBOOK_LINK.'" target="_blank" class="facebook"></a></li>
																	<li><a href="'.LIONSCRIPTS_TWITTER_LINK.'" target="_blank" class="twitter"></a></li>
																	<li><a href="'.LIONSCRIPTS_GOOGLE_PLUS_LINK.'" target="_blank" class="gplusdark"></a></li>
																	<li><a href="'.LIONSCRIPTS_YOUTUBE_LINK.'" target="_blank" class="youtube"></a></li>
																	<li><a href="'.LIONSCRIPTS_HOME_PAGE_URL.'shop/feed" target="_blank" class="rss"></a></li>
																</ul>
																<div class="cl"></div>
															</p>';


if (!empty($_SERVER['HTTP_CLIENT_IP']))
    $ip = $_SERVER['HTTP_CLIENT_IP'];
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
    $ip = $_SERVER['REMOTE_ADDR'];

if(!defined('LIONSCRIPTS_CURRENT_USER_IP'))
	define('LIONSCRIPTS_CURRENT_USER_IP', $ip);

if(!is_admin())
{
	global $LIONSCRIPTS;
	$check_current_user_ip = $objLionTemp->check_blocked_ip();
	
	if(($check_current_user_ip == 1) && !preg_match('/wp-login.php/i', $_SERVER['REQUEST_URI']))
	{
		$notice = "Your IP is Banned by the Administrator."; $comments = "<!-- Protected By ".$objLionTemp->plg_name." - ".$objLionTemp->plg_product_url." -->";
		$objLionTemp->get_configuration();

		if( isset($LIONSCRIPTS[$objLionTemp->plg_identifier]) && ($LIONSCRIPTS[$objLionTemp->plg_identifier]['show_blank_page_to_banned_user'] == 1) )
		{
			echo $comments.'<br /><br /><br /><br />'.$objLionTemp->attr_display(true);
		}
		else
		{
			?><!DOCTYPE html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="robots" content="noindex,nofollow" /><title><?php echo $notice; ?></title></head><body><?php echo $comments; ?><br /><br /><br /><br /><center><?php echo $notice.'<br /><br /><br /><br />'.$objLionTemp->attr_display(true); ?></center></body></html><?php 
		}
		die();
	}
}

if(!function_exists('lionscripts_wib_uninstall'))
{
	function lionscripts_wib_uninstall()
	{
		global $wpdb, $objLionTemp;
		if(isset($objLionTemp->plg_table['ip']))
		{
			$objLionTemp->deactivate();
			$sql = "DROP TABLE ".$objLionTemp->plg_table['ip'].";";
			$wpdb->query($sql);
		}
	} 
}

unset($objLionTemp);

?>