<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$warehouse = new itsmcore\Warehouse;
		$create = $warehouse->create($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "inventory/settings/warehouses/view?w=" . $warehouse->getId() . "&NOTICE=Warehouse Created"));
		else $faSystemErrors[] = "Could Not Create Warehouse";
	}
	?>
	<div class="button-bar">
		<span id="warehouse" class="button form-submit-button" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/warehouseform.php");
?>