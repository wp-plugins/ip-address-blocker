<?php

if(!class_exists('lionscripts_plg'))
{
	class lionscripts_plg
	{
		var $plg_name					= '';
		var $plg_name_2					= '';
		var $plg_url_val				= '';
		var $plg_product_url			= '';
		var $plg_description			= '';
		var $plg_version				= '';
		var $plg_hook_version			= '';
		var $plg_name_pro				= '';
		var $plg_heading				= '';
		var $plg_short_name				= '';
		var $plg_identifier				= '';
		var $plg_attr					= '';
		var $plg_table					= array();
		var $plg_db_var					= array();
		var $plg_base					= array();
		var $plg_assets					= array();
		var $plg_css					= array();
		var $plg_images					= array();
		var $plg_javascript				= array();
		var $site_base 					= array();
		var $site_admin_url 			= '';
		var $site_admin_dashboard_url 	= '';
		var $plg_redirect_const			= '';
		var $plg_db_version_const		= '';
		
		public function use_thickbox()
		{
			add_thickbox();
		}
		
	}
}

if(!function_exists('debug'))
{
	function debug($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}


?>