<?php
/*
Plugin Name: Postcode Price Calculator
Plugin URI: http://www.codeflox.com/
Description: Campaing Approval.
Version:0.0.1
Tested up to:4.9.8
Author: CodeFLox
Author URI: http://www.manojsinghrwt.wordpress.com
Text Domain:pcpc
Domain Path:lang/
Network:true
License:GPLv3 or later
License URI:http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
* Bloack direct access.
*
* @since  0.0.1
*/
if(!defined('ABSPATH')){
	exit;
}
define("PCPC_PLUGIN_DIR",plugin_dir_url(__FILE__));
define("PCPC_PLUGIN_PATH",plugin_dir_path(__FILE__));
require_once('includes/wppostcode.php');
require('includes/postcodes-io-api.php');
class PCPC_Init{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	public function __construct(){
		add_action('wp_head',array($this,'wppcpc_object'));
		add_action('wp_enqueue_scripts',array($this,'js'));
		add_action('wp_enqueue_scripts',array($this,'css'));
		#add_action('admin_enqueue_scripts',array($this,'js'));
		#add_action('admin_enqueue_scripts',array($this,'css'));
	}
	public function css(){
		wp_register_style('pcpccss',PCPC_PLUGIN_DIR.'css/pcpc.css');
		wp_enqueue_style('pcpccss');
		wp_register_style('sweetalert2css',PCPC_PLUGIN_DIR.'css/sweetalert2.min.css',false,'7.24.3');
    	wp_enqueue_style('sweetalert2css' );
	}
	public function js(){
		wp_register_script('sweetalert2js',PCPC_PLUGIN_DIR.'js/sweetalert2.min.js',array('jquery'),'7.24.3',true);
		wp_enqueue_script('sweetalert2js');
		wp_register_script('pcpcjs',PCPC_PLUGIN_DIR.'js/pcpc.js',array('jquery'),'0.0.1',true);
		wp_enqueue_script('pcpcjs');
	}
	public function wppcpc_object(){
		?><script>var wppcpcobj={"ajaxurl":"<?php echo admin_url('admin-ajax.php'); ?>"}</script><?php
	}
}
add_action('plugins_loaded',array('PCPC_Init','init'));