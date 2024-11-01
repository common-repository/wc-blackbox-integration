<?php
/*
Plugin Name: WC Blackbox Integration
Description: Интеграция сервиса непорядочных покупателей Blackbox в ваш WooCommerce магазин
Version: 1.2
Author: Evgen "EvgenDob" Dobrzhanskiy
Author URI: http://voodoopress.net
Stable tag: 1.2
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');


// core initiation
if( !class_Exists('blackboxAPIMain') ){
	class blackboxAPIMain{
		public static $locale;
		function __construct( $locale, $includes, $path ){
			$this->locale = $locale;
			
			// include files
			foreach( $includes as $single_path ){
				include( $path.$single_path );				
			}
			// calling localization
			add_action('plugins_loaded', array( $this, 'myplugin_init' ) );
		}
		function myplugin_init() {
		 $plugin_dir = basename(dirname(__FILE__));
		 load_plugin_textdomain( $this->locale , false, $plugin_dir );
		}
	}
	
	
}


// initiate main class

$obj = new blackboxAPIMain('wwb', array(
	'modules/hooks.php',
), dirname(__FILE__).'/' );
 
 





 
?>