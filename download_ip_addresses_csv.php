<?php
global $LIONSCRIPTS, $wpdb;
require_once( dirname(__FILE__).'/../../../'.'wp-load.php' );
if(isset($_GET['format']) && !empty($_GET['format']))
{
	$blocked_ips_list = lionscripts_wib_prepare_all_blocked_ips();
	$blocked_ips_list_arr = array();
	
	if($_GET['format'] == 'wib_pro')
	{
		$header = 'IP Address,Block Type,Notes,Block Date From,Block Date To';
		$rows = '';
		foreach($blocked_ips_list as $key=>$value)
		{
			$blocked_ips_list_arr[$value->id] = $value->ip;
			$rows .= $value->ip.','.'Permanent'.','.'Blocked IP. (Lite Version Block ID-'.$value->id.')'.','.''.','.''."\n";
		}
		echo $header."\n".$rows;
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=IP_Address_Blocker_Lite_Bkp_'.(date('d_M_Y')).'_In_WordPress_IP_Blocker_Pro_CSV_Format'.'.csv');
		header('Pragma: no-cache');
		exit;
	}
	else
	{
		$header = 'IP Address';
		$rows = '';
		foreach($blocked_ips_list as $key=>$value)
		{
			$blocked_ips_list_arr[$value->id] = $value->ip;
			$rows .= $value->ip."\n";
		}
		echo $header."\n".$rows;
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=IP_Address_Blocker_Lite_Bkp_'.(date('d_M_Y')).'_In_Normal_CSV_Format'.'.csv');
		header('Pragma: no-cache');
		exit;
	}
}
?>