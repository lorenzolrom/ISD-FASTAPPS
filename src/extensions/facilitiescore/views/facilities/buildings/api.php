<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['building'])) // Get details for specific building
	{		
		$building = new facilitiescore\Building();
		
		if($building->loadFromCode($_GET['building']))
		{
			$buildingAttributes['name'] = $building->getName();
			$buildingAttributes['streetAddress'] = $building->getStreetAddress();
			$buildingAttributes['city'] = $building->getCity();
			$buildingAttributes['state'] = $building->getState();
			$buildingAttributes['zipCode'] = $building->getZipCode();
			
			$results = json_encode($buildingAttributes, JSON_HEX_QUOT);
		}
		else
			$results = '{}';
	}
	else if(isset($_GET['buildingCodes'])) // Get list of all building codes
	{
		$buildings = facilitiescore\getBuildings();
		
		$buildingCodes = [];
		
		foreach($buildings as $building)
		{
			$buildingCodes[] = $building->getCode();
		}
		
		$results = json_encode($buildingCodes, JSON_HEX_QUOT);
	}
	else
	{
		$results = '{}';
	}
?>
<div id="encoded-data">
	<?=ifSet($results)?>
</div>