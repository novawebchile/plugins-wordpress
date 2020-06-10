<section class="fecha-expiracion">
	<h3>Cantidad en Stock (<?php echo intval($product_stock); ?>)</h3>

	<?php for($i=0; $i<$product_stock; $i++)
	{ 
		$expiracion = isset($product_expirate[$i]) ? $product_expirate[$i] : '';
?>

	<strong>Fecha de Expiracion Producto (<?php echo ($i+1); ?>): </strong>
	<input type="text" class="product_expirate" name="product_expirate[]" value="<?php echo @$expiracion; ?>">
	<input type="hidden" name="product_message[]" value="no">
	<br>
	<br>

<?php } ?>

</section>
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$(".product_expirate").datepicker({ dateFormat: 'dd-mm-yy' });
	});
</script>