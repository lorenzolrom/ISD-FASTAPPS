<?php
	use facilitiesfloorplans as fp;
	
	if(!isset($_GET['f']))
		throw new AppException("Floorplan Not Defined", "P03");

	$floorplan = new fp\Floorplan($_GET['f']);
	if(!$floorplan->load())
		throw new AppException("Floorplan Not Found", "P04");
	
	if($floorplan->delete())
		exit(header("Location: " . getURI() . "/..?NOTICE=Floorplan Deleted"));
	else
		$faSystemErrors[] = "Could Not Delete Floorplan";
