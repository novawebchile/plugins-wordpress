<?php 

/*add_filter('woocommerce_product_get_price', 'custom_price_by_taza_dolar_bs', 99, 2 );
add_filter('woocommerce_product_get_sale_price', 'custom_price_by_taza_dolar_bs', 99, 2 );
// Variations
add_filter('woocommerce_product_variation_get_sale_price', 'custom_price_by_taza_dolar_bs', 99, 2 );
add_filter('woocommerce_product_variation_get_price', 'custom_price_by_taza_dolar_bs', 99, 2 );
function custom_price_by_taza_dolar_bs( $price, $product ) {

	echo "hola";

	if(!is_admin()){

		$price = 20;
	}


    return $price; 
	
}*/

/*function return_custom_price($price, $product) {
	if(is_cart() || is_checkout()){
		  // $complementos = $_SESSION['product_data_'.$product->get_id()];
		$price = 0;
	}

   return $price;
}
add_filter('woocommerce_get_price', 'return_custom_price', 10, 2);




add_action( 'woocommerce_cart_calculate_fees', 'prefix_add_discount_line', 10, 1 );
function prefix_add_discount_line( $cart ) {

	
	$fecha_hoy = date('Y-m-d');

	foreach(WC()->cart->get_cart() as $cart_item )
	{
			$price = 0;
			$cantidad_carrito = 0;
			$cont_precios = 0;
		    $product_id = $cart_item['product_id'];
		    $product_title = get_the_title($product_id);
		    $cantidad_carrito = $cart_item['quantity'];
	
			//aqui hacemos todo el calculo
			$_product = wc_get_product($product_id);
			$product_expirate = get_post_meta($product_id,'product_expirate',true);
			$product_stock = get_post_meta($product_id,'_stock',true);
			$_sale_price = $_product->get_sale_price();
			$_regular_price = $_product->get_regular_price();
			$cont_expirados = 0;
			if(isset($product_expirate) && !empty($product_expirate))
			{
				//veremos la cantidad en stock
				for($i=0; $i<$product_stock; $i++)
				{ 
					$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
					$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
					if($cantidad_dias<30){
						$cont_expirados++;
					}
				}//cierre del for
				//UNA VES SACADO LOS EXPIRADOS VEREMOS CUANTOS HAY 
				//vamos a centrarnos hasta la cantidad del carrito
				for($i=1; $i<=$cantidad_carrito;$i++)
				{
						if($cont_expirados>=$i)
						{
							//guardamos este precio
							$cont_precios = $cont_precios + floatval($_sale_price);
						}else{
							$cont_precios = $cont_precios + floatval($_regular_price);
						}
				}//cierre del for cantidad de carrito
			}
			//sacamos el total de precios completo
			$price = $price  + $cont_precios;
			$cart->add_fee( __( 'Producto '.$product_title, 'your-text-domain' ),$price);

	}//cierre del foreach activo
     //
}
*/



add_action('woocommerce_thankyou','woocommerce_thankyou_data_expired',10,1);
function woocommerce_thankyou_data_expired($order_id)
{
	$order = new WC_Order( $order_id );
	 //veremos si volamos la cantidad si no hay productos en expiracion quitamos la lista 
	$items = $order->get_items();
	$fecha_hoy = date('Y-m-d');
	
	foreach ( $items as $item ) {
		$cont_expirados = 0;
	    $product_name = $item['name'];
	    $product_id = $item['product_id'];
		
	    $product_expirate = get_post_meta($product_id,'product_expirate',true);
		$product_stock = get_post_meta($product_id,'_stock',true);
		$_product = wc_get_product($product_id);
		$_sale_price = $_product->get_sale_price();
		$_regular_price = $_product->get_regular_price();
		//buscamos si aun jhay expiraciones
		for($i=0; $i<$product_stock; $i++)
		{ 
			$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
			$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
			if($cantidad_dias<30)
			{
				$cont_expirados++;
			}
		}//cierre del for
		if($cont_expirados==0)
		{
			update_post_meta($product_id,'_sale_price','');
			$_product->set_sale_price('');
			$_product->save();
		}

	}//cierre del foreach

}