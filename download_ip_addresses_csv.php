<?php
global $LIONSCRIPTS, $wpdb;
require_once( dirname(__FILE__).'/../../../'.'wp-load.php' );
if(isset($_GET['format']) && !empty($_GET['format']))
{
	$objLionTemp = new lionscripts_plg_wib(basename(dirname(__FILE__)));
	$blocked_ips_list = $objLionTemp->get_all_blocked_ips();
	unset($objLionTemp);

	$blocked_ips_list_arr = array();
	
	if(($_GET['format'] == 'wib_pro'))
	{
		$header = 'IP Address,Block Type,Notes,Block Date From,Block Date To';
		$rows = '';
		
		if(isset($blocked_ips_list) && !empty($blocked_ips_list))
		{
			foreach($blocked_ips_list as $key=>$value)
			{
				$blocked_ips_list_arr[$key] = $value;
				$rows .= $value.','.'Permanent'.','.'Blocked IP. (Lite Version Block ID-'.$key.')'.','.''.','.''."\n";
			}
		}

		if(!headers_sent())
		{
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=IP_Address_Blocker_Lite_Bkp_'.(date('d_M_Y')).'_In_WordPress_IP_Blocker_Pro_CSV_Format'.'.csv');
			header('Pragma: no-cache');
		}

		echo $header."\n".$rows;
		exit;
	}
	else
	{
		$header = 'IP Address';
		$rows = '';

		if(isset($blocked_ips_list) && !empty($blocked_ips_list))
		{
			foreach($blocked_ips_list as $key=>$value)
			{
				$blocked_ips_list_arr[$key] = $value;
				$rows .= $value."\n";
			}
		}
		
		if(!headers_sent())
		{
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=IP_Address_Blocker_Lite_Bkp_'.(date('d_M_Y')).'_In_Normal_CSV_Format'.'.csv');
			header('Pragma: no-cache');
		}

		echo $header."\n".$rows;
		exit;
	}
}
?>