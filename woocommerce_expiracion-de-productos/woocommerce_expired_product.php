<?php 

/*
	Plugin Name: EXPIRACIONES PRODUCTOS  WORDPRESS
	Plugin URI: https.noivawen.cl
	Description: Plugin para notificar expiracion de productos
	Version: 1.0
	Author: Novaweb
	Author URI: https://novaweb.cl
	License: GPLv2
*/


if ( ! class_exists( 'WooCommerce', false ) ) 
{
	// no hara nimguna accion

}else{


if (!defined('WPEXPIRED_PRODUCT_PLUGIN_DIR')) 
{
	define('WPEXPIRED_PRODUCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}

// Plugin Folder URL
if (!defined('WPEXPIRED_PRODUCT_PLUGIN_URL')) 
{
	define('WPEXPIRED_PRODUCT_PLUGIN_URL',plugin_dir_url( __FILE__ ));
}



//Elementos a incluir
//require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/postype.php';
require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/metabox.php';
require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/settings.php';
//require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/actions_filters.php';
//require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/shortcode.php';

}


function plugin_loaded_expired_product()
{
	if (!class_exists( 'WooCommerce', false )) 
	{	
		echo '<div class="notice notice-error is-dismissible">
			<p><strong>EXPIRACION DE  PRODUCTOS WORDPRESS</strong> DEBE INSTALAR  <strong>WOOCOMMERCE</strong> PARA PODER ACTIVARSE</p>
			</div>';
		//desactivamos el plugin
		deactivate_plugins( '/woocommerce_expired_product/woocommerce_expired_product.php' );
	}
}
add_action('admin_notices', 'plugin_loaded_expired_product');
