<?php
	use itsmcore as itsmcore;
	
	$results = [];
	
	if(isset($_GET['vendor'])) //  Get details for specific vendor
	{
		$vendor = new itsmcore\Vendor();
		
		if($vendor->loadFromCode($_GET['vendor']))
		{
			$vendorAttributes['name'] = $vendor->getName();
			
			$results = $vendorAttributes;
		}

	}
	else if(isset($_GET['vendorCodes'])) // Get list of all vendor codes
	{
		$vendors = itsmcore\getVendors();
		
		$vendorCodes = [];
		
		foreach($vendors as $vendor)
		{
			$vendorCodes[] = $vendor->getCode();
		}
		
		$results = $vendorCodes;
	}
	else if(isset($_GET['warehouse'])) //  Get details for specific warehouse
	{
		$warehouse = new itsmcore\Warehouse();
		
		if($warehouse->loadFromCode($_GET['warehouse']))
		{
			$warehouseAttributes['name'] = $warehouse->getName();
			
			$results = $warehouseAttributes;
		}

	}
	else if(isset($_GET['warehouseCodes'])) // Get list of all warehouse codes
	{
		$warehouses = itsmcore\getWarehouses();
		
		$warehouseCodes = [];
		
		foreach($warehouses as $warehouse)
		{
			$warehouseCodes[] = $warehouse->getCode();
		}
		
		$results = $warehouseCodes;
	}
	
	$results = json_encode($results, JSON_HEX_QUOT);
?>
<div id="encoded-data">
	<?=ifSet($results)?>
</div>