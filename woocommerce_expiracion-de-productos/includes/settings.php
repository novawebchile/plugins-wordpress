<?php 

	add_action('admin_menu','wooexpired_product_menu');
	function wooexpired_product_menu()
	{
		add_menu_page(
			'caducados', 'Productos Caducados', 'manage_options', 
			'caducados', 'product_expired_fn', ' dashicons-megaphone', 75
		);
	}




function product_expired_fn()
{
	require_once WPEXPIRED_PRODUCT_PLUGIN_DIR.'includes/view/settings.php';
}	


//guardar los datos de expiracion
add_action('admin_post_action_expired_product','action_expired_product_callback');
add_action('admin_post_nopriv_action_expired_product','action_expired_product_callback');
function  action_expired_product_callback()
{
	$product_expirate_email = $_POST['product_expirate_email'];
	$product_expirate_asunto = $_POST['product_expirate_asunto'];
	//==================>
	update_option('product_expirate_email',$product_expirate_email);
	update_option('product_expirate_asunto',$product_expirate_asunto);
	//================
	wp_redirect(wp_get_referer());
}




//Funcion para verificar fecha de expiracion y enviar al correo electronico 
add_action('admin_head','verify_products_expirated');
function verify_products_expirated()
{	

		$cont_email_expirate = 0;
		$product_expirate_email = get_option('product_expirate_email',true);
		$product_expirate_asunto = get_option('product_expirate_asunto',true);
		$product_expirate_mensaje = '<h3>Tienes productos en rigor de vencimiento</h3><p>Te mostramos la lista de productos que pronto estaran en expiración</p>';
		$product_expirate_mensaje.='<ol>';


		$fecha_hoy = date('Y-m-d');
		$cont_expiracion = 0;
		//Mostraremos los productos
		$args = array(
			'post_type'=>'product',
			'post_status'=>'publish',
			'posts_per_page'=>-1,
		);
		//vamos a buscar por productos y la fecha de hoy
		$productos = get_posts($args);
		foreach($productos as $producto)
		{
			$product_expirate = get_post_meta($producto->ID,'product_expirate',true);
			$product_mensaje = get_post_meta($producto->ID,'product_message',true);
			$product_stock = get_post_meta($producto->ID,'_stock',true);
			$_product = wc_get_product($producto->ID);
		    $_sale_price = $_product->get_sale_price();

			//colocando la url del producto
			$url_producto =  get_home_url().'/wp-admin/post.php?post='.$producto->ID.'&action=edit';

			//para expirar
			if(isset($product_expirate) && !empty($product_expirate))
			{ 
				for($i=0; $i<$product_stock; $i++)
				{ 
					$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
					$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
					//$cantidad_dias = verificate_dates($fecha_hoy,$product_expirate);
					if(intval($cantidad_dias)<30)
					{
						break; // salimos , solo nesecitamos uno solo para ver que hay una noticia de expiracion
					}
				}
				//veremos la 
				//si es menor a 30 dias el producto Esta por expirar
				
				if(intval($cantidad_dias)<30)
				{

					//si es distinto de vacio es que ya fue ofertado, no entra en la lista de expiraciones
					if($_sale_price!='')
					{
						//no entra aqui
						
					}else{
						$cont_expiracion++;
						if($product_mensaje!='yes')
						{
							$cont_email_expirate++;
							//vamos rellenando los productos 
							$product_expirate_mensaje.='<li> Producto: '.$producto->post_title.' -- <a href="'.$url_producto.'" target="_blank">Ir a ver</a></li>';

							//cambiamos el status del producto
							update_post_meta($producto->ID,'product_message','yes');
						}
					}


				}
			}	
		}//cierre del foreach
		$product_expirate_mensaje.='</ol>';


		//veremos si es mayor a 0 enviamos el mensaje
		if($cont_email_expirate>0)
		{
			//=======Una ves hecho esto se enviara el mensaje al medico
			//para
			$to = $product_expirate_email;
			//asunto
			$subject = $product_expirate_asunto;
			//comentario
			$body = $product_expirate_mensaje;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			//enviamos el mensaje
			wp_mail( $to, $subject, $body, $headers );
		} 

	if(count($_POST)>0 || @$_GET['page']=='caducados')
	{
		//no entras aqui -> por lo que no pasa nada
	}else{
	if($cont_expiracion>0 && is_admin())
	{

		$url = get_home_url().'/wp-admin/admin.php?page=caducados';
	?>
		<style type="text/css">
			.box-expired{
				background-color: #bf2016;
				width: 100%;
				padding: 15px;
				text-align: left;
				position: fixed;
				left: 0;
				bottom: 0;
				z-index: 9999999999999999;
				color:white;
				font-weight: bold;

			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var url = "<?php echo $url; ?>";
				var cont_expiracion = "<?php echo $cont_expiracion; ?>";

				$("body").append('<div class="box-expired"><span style="color:#ffeb3b; font-weight:bold;">ALERTA</span> Tienes  '+cont_expiracion+' producto/s por Expirar <a href="'+url+'" class="button button-primary" style="margin-left:15px;">Ver lista</a><a href="#" class="cerrar-box" style="color:white; font-weight:bold; float:right; margin-right:50px;">Cerrar</a></div>');
			
				jQuery(document).on('click','.cerrar-box',function()
				{
					$(".box-expired").fadeOut(500);
				});
			});

		</script>
	<?php 
		}
	}	

}


//verificaremos cantidad de dias entre dos fechas
function verificate_dates($fecha_hoy , $fecha_product)
{
		
	/*$date1 = new DateTime($fecha_hoy);
	$date2 = new DateTime($fecha_product);
	$diff = $date1->diff($date2);
	// will output 2 days
	//echo "--------------------------------------------------".$diff->days . ' days ';
	return $diff->days;*/
	
	$data_fecha_hoy = explode('-',$fecha_hoy);
	$data_fecha_product = explode('-',$fecha_product);

	//defino fecha 1
	$ano1 = $data_fecha_hoy[0];
	$mes1 = $data_fecha_hoy[1];
	$dia1 = $data_fecha_hoy[2];


	//defino fecha 2 (VIENW DE JQUERY UI)
	$ano2 = $data_fecha_product[2];
	$mes2 = $data_fecha_product[1];
	$dia2 = $data_fecha_product[0];

	//calculo timestam de las dos fechas
	$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
	$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);

	//resto a una fecha la otra
	$segundos_diferencia = $timestamp1 - $timestamp2;
	//echo $segundos_diferencia;

	//convierto segundos en días
	$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

	//obtengo el valor absoulto de los días (quito el posible signo negativo)
	$dias_diferencia = abs($dias_diferencia);

	//quito los decimales a los días de diferencia
	$dias_diferencia = floor($dias_diferencia);

	return $dias_diferencia;
}