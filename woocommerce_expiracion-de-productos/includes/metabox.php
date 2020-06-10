<?php 


	
	/*Metaboxes*/
	add_action('add_meta_boxes','wpexpired_metabox');
	function wpexpired_metabox()
	{	
		add_meta_box('wpexpired_box', 'Expiracion del producto','wpexpired_box_callback', array('product'), 'side', 'default');
	}



	//lista de archivos segun el historial
	function wpexpired_box_callback($post)
	{	
		$product_expirate = get_post_meta($post->ID,'product_expirate',true);
		$product_stock = get_post_meta($post->ID,'_stock',true);
		wp_enqueue_script('jqueryUI','https://code.jquery.com/ui/1.12.1/jquery-ui.js',array('jquery'));

		include_once 'view/date-expired.php';
	}

	//=====GUARDADO / ACTUALIZADO METABOX
	add_action('save_post_product','product_expired_save_post_callback',10,1);
	function product_expired_save_post_callback($post)
	{
	 	//guardamos lso datos aqui del post
		$product_expirate = $_POST['product_expirate'];
		update_post_meta($post,'product_expirate',$product_expirate);
		//podra enviase el mensaje nuevamente
		update_post_meta($post,'product_message',$_POST['product_message']);

	}