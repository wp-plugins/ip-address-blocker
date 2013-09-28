<?php
/*
Plugin Name: LionScripts: IP Blocker Lite
Plugin URI: http://www.lionscripts.com/product/wordpress-ip-address-blocker-pro/
Description: LionScripts' IP Blocker for WordPress allows you to stop the Spam Visitors and malicious IP Addresses. You can block IP addresses by using the manual method or the Bulk IPs Upload method. By blocking the Unwanted or Spam IP Addresses, you can save your site's Bandwidth and hence the cost significantly. The blocked IPs won't be able to scrap the precious content from your WordPress Site. You can choose to either display the blocked message or an empty page to the blocked users. To do so, you can just add the IP Address to the blocking list and anytime you can delete that IP from the blocking list if you know that it's not performing malicious activities.
Version: 4.0
Stable Tag: 4.0
Author: LionScripts.com
Author URI: http://www.lionscripts.com/

*************************************************************************

Copyright (C) 2013 LionScripts.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*************************************************************************/

// Start LionScripts' IP Address Blocker (Lite)
global $LIONSCRIPTS, $wpdb;
define('LS_WP_BASE_NAME', get_bloginfo('wpurl'));
define('LIONSCRIPTS_WIB_BASE_NAME', LS_WP_BASE_NAME."/wp-content/plugins/".basename(dirname(__FILE__)));
require_once(dirname(__FILE__).'/wib.config.php');
function lionscripts_wib_get_wib_configuration()
{ 
	global $LIONSCRIPTS;
	$LIONSCRIPTS['WIB']['show_blank_page_to_banned_user'] = get_option('LionScriptsIPBlockerDisplayBlankPage'); 
	$LIONSCRIPTS['WIB']['show_lionscripts_attribution'] = get_option('LionScriptsIPBlockerDisplayAttribution'); 
	return $LIONSCRIPTS['WIB'];
}

function lionscripts_wib_ip_address_blocker_install()
{ 
	global $wpdb; 
	$sql = "CREATE TABLE ".LIONSCRIPTS_WIB_BASIC_TABLE_NAME." (id int(12) NOT NULL AUTO_INCREMENT, ip VARCHAR(255) DEFAULT '' NOT NULL, UNIQUE KEY id (id));"; 
	require_once(ABSPATH.'wp-admin/includes/upgrade.php'); 
	dbDelta($sql); 
	add_option("ip_address_blocker_db_version", LIONSCRIPTS_WIB_BASIC_DB_VERSION); 
	register_setting('wib_activate_redirect', 'wib_activate_redirect'); 
	add_option("wib_activate_redirect", true); 
} 

function lionscripts_wib_ip_address_blocker_uninstall()
{ 
	global $wpdb;
	$sql = "DROP TABLE ".LIONSCRIPTS_WIB_BASIC_TABLE_NAME.";";
	$wpdb->query($sql);
	delete_option("ip_address_blocker_db_version");
	delete_option("wib_activate_redirect");
} 

function lionscripts_wib_basic_settings_link($links)
{
	$settings_link = '<a href="admin.php?page='.LIONSCRIPTS_WIB_BASIC_URL_NAME.'">Settings</a>';
	array_unshift($links, $settings_link); return $links; 
}

function lionscripts_wib_admin_settings_page()
{ 
	if (get_option('wib_activate_redirect', false)) 
	{ 
		delete_option('wib_activate_redirect');
		wp_redirect(LIONSCRIPTS_WIB_ADMIN_URL);
	}
}
		
function lionscripts_wib_add_ip_to_db($ip)
{
	global $wpdb; 
	$rows_affected = $wpdb->insert( LIONSCRIPTS_WIB_BASIC_TABLE_NAME, array( 'ip'=>$ip ) );
}

function lionscripts_wib_prepare_all_blocked_ips()
{ 
	global $wpdb;
	$result = $wpdb->get_results("SELECT * FROM ".LIONSCRIPTS_WIB_BASIC_TABLE_NAME);
	return $result;
}

function lionscripts_wib_get_all_blocked_ips()
{
	$blocked_ips_data = lionscripts_wib_prepare_all_blocked_ips();
	foreach($blocked_ips_data as $ip_data)
	{
	$ip[$ip_data->id] = $ip_data->ip;
	}
	return $ip;
}

function lionscripts_wib_delete_ip($id)
{
	global $wpdb;
	$sql = $wpdb->query("DELETE FROM ".LIONSCRIPTS_WIB_BASIC_TABLE_NAME." WHERE id = $id");
	$wpdb->query($sql);
}

function lionscripts_wib_show_admin_menu()
{
	add_menu_page(LIONSCRIPTS_WIB_BASIC_FULL_NAME, LIONSCRIPTS_WIB_BASIC_SHORT_NAME, 10, LIONSCRIPTS_WIB_BASIC_URL_NAME, 'lionscripts_wib_wp_ip_blocker', LIONSCRIPTS_WIB_BASIC_BASE."/images/".LIONSCRIPTS_WIB_BASIC_URL_NAME."-16.png"); 
}

function yoursite_template_redirect()
{
	echo "Here";
	if(isset($_GET['download_csv']) && !empty($_GET['download_csv']))
	{
	}
	if ($_SERVER['REQUEST_URI']=='/downloads/data.csv')
	{
	}
}

function lionscripts_wib_wp_ip_blocker()
{
	global $LIONSCRIPTS;
	?>
	<style type="text/css"> 
	div#wipblocker-heading1
	{
		background:url('<?php echo LIONSCRIPTS_WIB_BASIC_BASE."/images/".LIONSCRIPTS_WIB_BASIC_URL_NAME."-32.png"; ?>') no-repeat;
	}
    </style>
	<?php 
	if($_POST)
	{ 
		$blocked_ips_list = lionscripts_wib_get_all_blocked_ips();
		
		if(isset($_GET['block_type']) && ($_GET['block_type'] == 'upload'))
		{
			$uploaded_ips_csv = lionscripts_wib_uploader('input_ips_csv_upload_lite');
			if(isset($uploaded_ips_csv) && !empty($uploaded_ips_csv))
			{
				$handle = fopen($uploaded_ips_csv['file_full_path'], "r");
				while (($data = fgetcsv($handle)) !== FALSE)
				{
					if($data[0] != 'IP Address')
					{
						$csv_ip_address = $data[0];
						$save_ip_by_csv[] = lionscripts_wib_add_ip_to_db($csv_ip_address);
						
					}
				}
			}
		}
		
		if(!empty($_POST['new-ip-1']))
		{
			$ip_address = $_POST['new-ip-1']; 
		}
		else if(!empty($_POST['new-ip-2']))
		{
			$ip_address = $_POST['new-ip-2'];
		}
		
		if(isset($save_ip_by_csv) && !empty($save_ip_by_csv))
		{
			$response = '<center><b><font style="color:blue">IPs CSV has been successfully uploaded</font></b></center>';
		}
		else if(isset($ip_address))
		{
			if((!is_array($blocked_ips_list)) || !(in_array($ip_address, $blocked_ips_list)))
			{
				$added = lionscripts_wib_add_ip_to_db($ip_address);
				$response = '<center><b><font style="color:blue">Provided IP Address has been added successfully</font></b></center>'; 
			}
			else
			{ $response = '<center><b><font style="color:red">Provided IP Address is already there in the Blocking List</font></b></center>'; }
		}
		else
		{
			$response = '<center><b><font style="color:blue">Settings has been successfully updated</font></b></center>';
		}
		
		if(isset($_GET['block_type']) && ($_GET['block_type'] == 'configuration'))
		{
			update_option('LionScriptsIPBlockerDisplayBlankPage', $_POST['show_blank_page_to_banned_user']);
			update_option('LionScriptsIPBlockerDisplayAttribution',$_POST['show_lionscripts_attribution']);
		}
	}
	
	$LIONSCRIPTS['WIB']['show_blank_page_to_banned_user'] = get_option('LionScriptsIPBlockerDisplayBlankPage');
	$LIONSCRIPTS['WIB']['show_lionscripts_attribution'] = get_option('LionScriptsIPBlockerDisplayAttribution');
	
	if($_GET['delete_ip'])
	{
		$delete_ip = lionscripts_wib_delete_ip($_GET['delete_ip']);
		$response = '<center><b><font style="color:red">Provided IP Address has been successfully deleted from the Blocking List</font></b></center>';
	}
	$blocked_ips_list = lionscripts_wib_get_all_blocked_ips();
	?>
    <div class="wrap" style="margin-left:1%;">
    	<h2><?php echo LIONSCRIPTS_WIB_BASIC_FULL_NAME_HEADING; ?> - Settings</h2>
    	<div style="width:70%;float:left;">
    		<div id="wib_settings">
            	Plugin Version: <b><font style="color:#008000"><?php echo LIONSCRIPTS_WIB_BASIC_DB_VERSION; ?></font> <font style="color:#800000">[Lite Version]</font></b> &nbsp; | &nbsp;<b>
                <a style="text-decoration:none;" href="<?php echo LIONSCRIPTS_WIB_BASIC_FULL_PAGE_LINK; ?>" target="_blank" title="Buy the <?php echo LIONSCRIPTS_WIB_BASIC_FULL_NAME_PRO; ?>">Purchase <?php echo LIONSCRIPTS_WIB_BASIC_FULL_NAME_PRO; ?> ? </a></b>
                <br /><br />
                	<b>Your current IP Address is </b><?php echo LIONSCRIPTS_WIB_BASIC_CURRENT_USER_IP; ?> , <b><font title="You will be unable to view your site if you block your own IP Address.">Please do not block your own IP.</font></b>
                
                <br /><br />
				<?php 
                if(isset($response))
                {
                    echo $response.'<br />';
                }
                ?> 
                <form action="admin.php?page=<?php echo LIONSCRIPTS_WIB_BASIC_URL_NAME; ?>&block_type=configuration" method="post">
                    <p>
                        <input type="checkbox" name="show_blank_page_to_banned_user" id="show_blank_page_to_banned_user" onClick="this.form.submit()" value="1" <?php if($LIONSCRIPTS['WIB']['show_blank_page_to_banned_user'] == 1) { echo('checked="checked"'); } ?> />
                        <label for="show_blank_page_to_banned_user"> Display blank page to the Banned User</label>
                    </p>
                    <p>
                        <input type="checkbox" name="show_lionscripts_attribution" id="show_lionscripts_attribution" value="1" onClick="this.form.submit()" <?php if($LIONSCRIPTS['WIB']['show_lionscripts_attribution'] == 1) { echo('checked="checked"'); } ?> />
                        <label for="show_lionscripts_attribution"> Proudly display that you are using <?php echo LIONSCRIPTS_WIB_BASIC_FULL_NAME; ?></label>
                    </p>
                    <input type="hidden" name="submit_form" value="submit_form" />
                </form>

                <div id="wib_blocking_option">
                	<label><input type="radio" name="ip_blocking_type" id="ip_blocking_type_manual" onClick="jQuery('#manual_ip_block_wib_lite').show();jQuery('#upload_ips_wib_lite').hide();jQuery('#download_ips_wib_lite').hide();" checked /> Block IPs Manually</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	<label><input type="radio" name="ip_blocking_type" id="ip_blocking_type_upload" onClick="jQuery('#manual_ip_block_wib_lite').hide();jQuery('#upload_ips_wib_lite').show();jQuery('#download_ips_wib_lite').hide();" /> Upload IP Addresses (CSV Format)</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	<label><input type="radio" name="ip_blocking_type" id="ip_blocking_type_download" onClick="jQuery('#manual_ip_block_wib_lite').hide();jQuery('#upload_ips_wib_lite').hide();jQuery('#download_ips_wib_lite').show();" /> Download Blocked IP Addresses (CSV Format)</label>
                </div>

                <div id="manual_ip_block_wib_lite">
                	<form action="admin.php?page=<?php echo LIONSCRIPTS_WIB_BASIC_URL_NAME; ?>" method="post">
                        <p>
                            Add New IP: 
                            <input type="text" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" name="new-ip-1" id="new-ip-1" style="width:37%" value="" />
                            <input type="submit" class="button-secondary" value="Add" />
                            <!--<input type="button" class="button-secondary" value="Download CSV" />-->
                            <input type="submit" style="float:right;" class="button-primary" value="Save Changes" />
                        </p>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">S.No.</th>
                                    <th style="width: 110px;text-align: center;">IP Address</th>
                                    <th style="width: 20px;text-align: center;">Delete</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>S.No.</th>
                                    <th style="text-align: center;">IP Address</th>
                                    <th style="text-align: center;">Delete</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php if(!isset($blocked_ips_list) && ($blocked_ips_list == ''))
                                {
                                ?>
                                    <tr id="no_saved_data">
                                        <td>&nbsp;</td>
                                        <td style="text-align: center;">No saved data Exists.</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                <?php
                                }
                                else
                                {
                                    $i=1;
                                    foreach($blocked_ips_list as $key=>$ip_data)
                                    {
                                    ?>
                                        <tr id="blocked_ip_<?php echo $ip_data->id; ?>" class="blocked_ips_data">
                                            <td><?php echo $i; ?></td>
                                            <td style="text-align: center;"><?php echo $ip_data; ?></td>
                                            <td style="text-align: center;"><a href="admin.php?page=<?php echo LIONSCRIPTS_WIB_BASIC_URL_NAME; ?>&delete_ip=<?php echo $key; ?>" onClick="return confirm('Are you sure about deleting IP <?php echo $ip_data; ?> ?');"><img src="<?php echo LIONSCRIPTS_WIB_BASIC_BASE."/images/".LIONSCRIPTS_WIB_BASIC_URL_NAME."-delete-16.png"; ?>"/></a></td>
                                        </tr>
                                        <?php 
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <p>
                            Add New IP: <input type="text" name="new-ip-2" id="new-ip-2" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" style="width:37%" value="" />
                            <input type="submit" class="button-secondary" value="Add" />
                            <input type="submit" style="float:right;" class="button-primary" value="Save Changes" />
                        </p>
                    </form>
                </div>
                
                <div id="upload_ips_wib_lite">
                	<br />
                	<form id="csv_ip_adder" name="csv_ip_adder" method="post" action="admin.php?page=<?php echo LIONSCRIPTS_WIB_BASIC_URL_NAME; ?>&block_type=upload" method="post" enctype="multipart/form-data">
                        <p>
                            <input type="file" accept="text/csv" required="required" name="input_ips_csv_upload_lite" id="input_ips_csv_upload_lite" />
                            &nbsp;&nbsp;
                            <input id="submit" name="submit" type="submit" class="button-primary" value="Upload CSV" />
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            ( <a href="<?php echo LIONSCRIPTS_WIB_BASE_NAME; ?>/sample-ips-upload-lite-version.csv">Download Sample CSV</a> )
                        </p>
                    </form>
                </div>

                <div id="download_ips_wib_lite">
                	<br />
                    <p>
                        <a href="<?php echo LIONSCRIPTS_WIB_BASE_NAME.'/download_ip_addresses_csv.php?format=wib_pro'; ?>" class="button-primary">Download in IP Blocker Pro Format</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo LIONSCRIPTS_WIB_BASE_NAME.'/download_ip_addresses_csv.php?format=normal'; ?>" class="button-secondary">Download in Normal CSV Format</a>
                    </p>
                </div>

                <br />
                <p>
                	<a href="<?php echo LIONSCRIPTS_WIB_BASIC_FULL_PAGE_LINK; ?>" target="_blank" style="text-decoration:none;"><b>See the difference</b> between <b>Lite</b> and <b>Professional</b> Versions of <b><?php echo LIONSCRIPTS_WIB_BASIC_FULL_NAME_2; ?></b>.</a>
                    <br /><br />
                    <small>For all kind of Inquiries and Support, please visit at <a href="http://<?php echo LIONSCRIPTS_WIB_BASIC_SUPPORT_WEBSITE; ?>" target="_blank"><?php echo LIONSCRIPTS_WIB_BASIC_SUPPORT_WEBSITE; ?></a>.</small>
                    <br /><br />
                    <a href="<?php echo LIONSCRIPTS_WIB_BASIC_TWITTER_LINK; ?>" style="text-decoration:none;" target="_blank"><img src="<?php echo LIONSCRIPTS_WIB_BASIC_BASE."/images/twitter.png"; ?>"/></a>
                    <a href="<?php echo LIONSCRIPTS_WIB_BASIC_FACEBOOK_LINK; ?>" style="text-decoration:none;" target="_blank"><img src="<?php echo LIONSCRIPTS_WIB_BASIC_BASE."/images/facebook.png"; ?>"/></a>
                </p>
            </div>
        </div>
        
        <div id="ip_address_blocker" style="float:left;margin-left:2%;">
        	<a href="<?php echo LIONSCRIPTS_WIB_BASIC_FULL_PAGE_LINK; ?>" target="_blank"><img src="<?php echo LIONSCRIPTS_WIB_BASIC_BASE."/images/".LIONSCRIPTS_WIB_BASIC_URL_NAME."-pro.png"; ?>" border="0" /></a>
        </div>
    </div>
	<script type="text/javascript">
	(function($)
	{
		$(document).ready(function(e) {
			$('#new-ip-1').focus();
			<?php
			if(isset($_GET['block_type']) && ($_GET['block_type'] == 'upload'))
			{
			?>
				$('#ip_blocking_type_upload').click();
			<?php
			}
			else
			{
			?>
				$('#ip_blocking_type_manual').click();
			<?php
			}
			?>
        });
	}
	)(jQuery);
    </script>
	<?php
}
	
function lionscripts_wib_block_user_ip()
{
	$blocked_ips_list = lionscripts_wib_get_all_blocked_ips();
	return $blocked_ips_list;
}

function lionscripts_wib_check_blocked_ip()
{
	$blocked_ips_list = lionscripts_wib_get_all_blocked_ips();
	$user_ip = LIONSCRIPTS_WIB_BASIC_CURRENT_USER_IP;
	if(is_array($blocked_ips_list) && in_array($user_ip, $blocked_ips_list))
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function lionscripts_wib_attr_display()
{
	$wib_attr_display = lionscripts_wib_get_wib_configuration();
	return (($wib_attr_display['show_lionscripts_attribution'] == 1) ? LIONSCRIPTS_WIB_BASIC_ATTRIB : '');
}

function lionscripts_wib_attr_foot_as_per_user_choice()
{
	$wib_attr_display = lionscripts_wib_get_wib_configuration();
	echo (($wib_attr_display['show_lionscripts_attribution'] == 1) ? LIONSCRIPTS_WIB_BASIC_ATTRIB : '');
}

function lionscripts_wib_plugin_is_active($plugin_var)
{
	return in_array( $plugin_var. '/' .$plugin_var. '.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}

function lionscripts_wib_uploader($f_name)
{
	$upload_dir = wp_upload_dir();
	$path = $upload_dir_path = $upload_dir['path'];
	if( isset($_FILES[$f_name]) && ($_FILES[$f_name] != '') && !($_FILES[$f_name]["error"] > 0) )
	{
		$file_name = $_FILES[$f_name]["name"];
		move_uploaded_file($_FILES[$f_name]["tmp_name"], $path.'/'.$file_name);
		$uploaded['file_name'] = $file_name;
		$uploaded['file_folder_name'] = $upload_dir_path;
		$uploaded['file_full_path'] = $upload_dir_path."/".$file_name;
		return $uploaded;
	}
	else
	{
		return false;
	}
}

if(is_admin() && lionscripts_wib_plugin_is_active(LIONSCRIPTS_WIB_BASIC_URL_NAME))
{
	add_action('admin_menu', 'lionscripts_wib_show_admin_menu');
}

if(!is_admin())
{
	global $LIONSCRIPTS;
	$check_current_user_ip = lionscripts_wib_check_blocked_ip();
	
	if(($check_current_user_ip == 1) && !preg_match('/wp-login.php/i', $_SERVER['REQUEST_URI']))
	{
		$notice = "Your IP is Banned by the Administrator."; $comments = "<!-- Protected By ".LIONSCRIPTS_WIB_BASIC_FULL_NAME." - ".LIONSCRIPTS_WIB_BASIC_FULL_PAGE_LINK." -->";
		$wib_attr_display = lionscripts_wib_get_wib_configuration();

		if($wib_attr_display['show_blank_page_to_banned_user'] == 1)
		{
			echo $comments.'<br /><br /><br /><br />'.lionscripts_wib_attr_display();
		}
		else
		{
			?><!DOCTYPE html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="robots" content="noindex,nofollow" /><title><?php echo $notice; ?></title></head><body><?php echo $comments; ?><br /><br /><br /><br /><center><?php echo $notice.'<br /><br /><br /><br />'.lionscripts_wib_attr_display(); ?></center></body></html><?php 
		}
		die();
	}
}

add_filter("plugin_action_links_".plugin_basename(__FILE__), 'lionscripts_wib_basic_settings_link' );
register_activation_hook(__FILE__, 'lionscripts_wib_ip_address_blocker_install');
register_uninstall_hook(__FILE__, 'lionscripts_wib_ip_address_blocker_uninstall');
add_action('admin_init', 'lionscripts_wib_admin_settings_page');
add_action('wp_footer', 'lionscripts_wib_attr_foot_as_per_user_choice');

// End LionScripts' IP Address Blocker (Lite)

?>