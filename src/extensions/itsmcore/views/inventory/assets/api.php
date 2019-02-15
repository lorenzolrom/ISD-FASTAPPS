<?php
	use facilitiescore as facilitiescore;
	
	$results = [];
	
	if(isset($_GET['building'])) // Get details for specific commodity
	{		
		$building = new facilitiescore\Building();
		
		if($building->loadFromCode($_GET['building']))
		{
			$buildingAttributes['name'] = $building->getName();
			
			$results = $buildingAttributes;
		}
	}
	else if(isset($_GET['buildingCodes'])) // Get list of all commodity codes
	{
		$buildings = facilitiescore\getBuildings();
		
		$buildingCodes = [];
		
		foreach($buildings as $building)
		{
			$buildingCodes[] = $building->getCode();
		}
		
		$results = $buildingCodes;
	}
	else if(isset($_GET['location']) AND isset($_GET['buildingCode'])) //  Get details for specific vendor
	{
		$location = new facilitiescore\Location();
		
		if($location->loadFromCode($_GET['buildingCode'], $_GET['location']))
		{
			$locationAttributes['name'] = $location->getName();
			
			$results = $locationAttributes;
		}

	}
	else if(isset($_GET['locationCodes'])) // Get list of all vendor codes
	{
		$building = new facilitiescore\Building();
		
		if($building->loadFromCode($_GET['locationCodes']))
		{
			$locationCodes = [];
			
			foreach($building->getLocations() as $location)
			{
				$locationCodes[] = $location->getCode();
			}
			
			$results = $locationCodes;
		}
	}
	else if(isset($_GET['warehouse']))
	{
		$warehouse = new itsmcore\Warehouse();
		
		if($warehouse->loadFromCode($_GET['warehouse']))
		{
			$warehouseAttributes['name'] = $warehouse->getName();
			
			$results = $warehouseAttributes;
		}
	}
	else if(isset($_GET['warehouseCodes']))
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