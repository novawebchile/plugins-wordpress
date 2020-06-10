<?php 

	$product_expirate_email = get_option('product_expirate_email',true);
	$product_expirate_asunto = get_option('product_expirate_asunto',true);

	$args = array(
		'post_type'=>'product',
		'post_status'=>'publish',
		'posts_per_page'=>-1,
	);
	$productos = get_posts($args);
?>

<style>
table {border-collapse: collapse;width: 100%;}
th, td {text-align: left; padding: 8px;background-color: white;}
tr:nth-child(even){background-color: #f2f2f2}
th {background: #a36597; border-color: #a36597;box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 1px 0 #a36597; color: white;}

</style>


<h1>Productos en Expiracion</h1>
<hr>

<form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
	<input type="hidden" name="action" value="action_expired_product">
	<h3>Notificarme al correo</h3>
	<p>Recibir Notificacion al correo electronico cuando un producto este en proceso de expiración</p>
	<input type="text" name="product_expirate_email" placeholder="Ingrese el correo" value="<?php echo @$product_expirate_email; ?>">
	<p><strong>Asunto del correo: </strong></p>
	<input type="" name="product_expirate_asunto" placeholder="Asunto del correo" value="<?php echo @$product_expirate_asunto; ?>">
	<br>
	<br>
	<button type="submit" class="button button-primary">Guardar Información</button>
</form>


<hr>
<h3>Lista de productos a expirar</h3>
<table width="90%">
	<tr>
		<th>ID</th>
		<th>NOMBRE</th>
		<th>PRECIO  / OFERTA</th>
		<th>Cantidad Productos</th>
		<th>FECHA DE EXPIRACION</th>
		<th>Expira en</th>
		<th>Estado</th>
		<th>--</th>
	</tr>
	<?php 

  		$fecha_hoy = date('Y-m-d');
		foreach($productos as $producto)
	{ 
		$_product = wc_get_product($producto->ID);
		$product_expirate = get_post_meta($producto->ID,'product_expirate',true);
		$product_stock = get_post_meta($producto->ID,'_stock',true);
		
		$_sale_price = $_product->get_sale_price();
		if(isset($product_expirate) && !empty($product_expirate))
		{
			//mini foreach
			for($i=0; $i<$product_stock; $i++)
			{ 
				$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
				$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
				if($cantidad_dias<30){
					break;
				}
			}

			//$cantidad_dias = verificate_dates($fecha_hoy,$product_expirate);
			if($cantidad_dias<30){
			
	?>

	<tr>
		<td><?php echo $producto->ID; ?></td>
		<td><?php echo $producto->post_title; ?></td>
		<td><?php echo $_product->get_regular_price().''.get_woocommerce_currency_symbol(); ?> <?php if($_sale_price!=''){ echo " / ".$_sale_price.''.get_woocommerce_currency_symbol(); } ?></td>
		<td><?php echo $product_stock; ?></td>
		<td>
			<?php

			 //echo date('d-m-Y',strtotime($product_expirate)); 
			for($i=0; $i<$product_stock; $i++)
			{ 
				$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
				$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
				if($cantidad_dias<30){
					echo date('d-m-Y',strtotime($expiracion)).'<br>';
				}
			}
			?>
		 	
		 </td>
		<td>
			<?php

			 //echo date('d-m-Y',strtotime($product_expirate)); 
			for($i=0; $i<$product_stock; $i++)
			{ 
				$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
				$cantidad_dias = verificate_dates($fecha_hoy,$expiracion);
				if($cantidad_dias<30){
					if($cantidad_dias<=0){
						echo "Ya expiro <br>";
					}else{
						echo $cantidad_dias. "Dia/s <br>";
					}
				}
			}
			?>
		</td>
		<td  <?php if($_sale_price!=''){ echo 'style="background-color:green;color:white;"'; }else{ echo 'style="background-color:red;color:white;"'; } ?>>

			<?php if($_sale_price!=''){ echo 'Ofertado'; }else{ echo 'No-Ofertado'; } ?></td>
		<td><a href="<?php echo get_home_url().'/wp-admin/post.php?post='.$producto->ID.'&action=edit';  ?>">Ver</a></td>
	</tr>
	<?php 	
			}//cierre del if 
			
		}
	}
	 ?>
</table>


<! -- script -->
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$("button.ver_respuesta").click(function(){
			$(".show_respuesta").show(0);
			$(this).hide(0);
		});
	

		 $(document).on('click','.mask',function()
	     {
	        $('.mask').fadeOut(500,function()
	         {
	             $(this).remove();
	         });
	    
	     });

	});
</script>