<?php namespace facilitiescore;
	////////////////////
	// ISD-FastApps
	// (c) 2018 LLR Technologies / Info. Systems Development
	// FacilitiesCore Extension Function File
	////////////////////
	
	/**
	* Returns an array of Buildings matching search criteria
	*/
	function getBuildings($codeFilter = "%", $nameFilter = "%", $streetAddressFilter = "%", $cityFilter = "%", $stateFilter = "%", $zipCodeFilter = "%")
	{
		global $conn;
		
		$queryString = "SELECT id FROM FacilitiesCore_Building WHERE code LIKE ? AND name LIKE ? AND streetAddress LIKE ? AND city LIKE ? AND state LIKE ? AND zipCode LIKE ? ORDER BY code ASC";
		
		$getBuildings = $conn->prepare($queryString);
		
		$getBuildings->bindParam(1, $codeFilter);
		$getBuildings->bindParam(2, $nameFilter);
		$getBuildings->bindParam(3, $streetAddressFilter);
		$getBuildings->bindParam(4, $cityFilter);
		$getBuildings->bindParam(5, $stateFilter);
		$getBuildings->bindParam(6, $zipCodeFilter);
		
		$getBuildings->execute();
		
		$buildings = [];
		
		foreach($getBuildings->fetchAll(\PDO::FETCH_COLUMN, 0) as $buildingId)
		{
			$building = new Building($buildingId);
			if($building->load())
				$buildings[] = $building;
		}
		
		return $buildings;
	}

	/**
	* Returns an array of Locations matching search criteria
	*/
	function getLocations($buildingFilter = "%", $codeFilter = "%", $nameFilter = "%")
	{
		global $conn;
		
		$queryString = "SELECT id FROM FacilitiesCore_Location WHERE building IN (SELECT id FROM FacilitiesCore_Building WHERE code LIKE ?) AND code LIKE ? AND name LIKE ? ORDER BY building,code ASC";
		
		$getLocations = $conn->prepare($queryString);
		
		$getLocations->bindParam(1, $buildingFilter);
		$getLocations->bindParam(2, $codeFilter);
		$getLocations->bindParam(3, $nameFilter);
		
		$getLocations->execute();
		
		$locations = [];
		
		foreach($getLocations->fetchAll(\PDO::FETCH_COLUMN, 0) as $locationId)
		{
			$location = new Location($locationId);
			if($location->load())
				$locations[] = $location;
		}
		
		return $locations;
	}
