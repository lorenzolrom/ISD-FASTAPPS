<?php namespace facilitiesfloorplans;

	function getFloorplans($buildingCodeFilter = "%", $floorFilter = "%")
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM Facilities_Floorplan 
			WHERE building IN (SELECT id FROM FacilitiesCore_Building WHERE code 
			LIKE ?) AND `floor` LIKE ? ORDER BY building, floor ASC");
		
		$get->bindParam(1, $buildingCodeFilter);
		$get->bindParam(2, $floorFilter);
		$get->execute();
		
		$floorplans = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$floorplan = new Floorplan($id);
			if($floorplan->load())
				$floorplans[] = $floorplan;
		}
		
		return $floorplans;
	}

