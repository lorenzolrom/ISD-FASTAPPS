<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['l']))
	{
		$location = new facilitiescore\Location($_GET['l']);
		
		if($location->load())
		{
			if($location->delete())
				exit(header("Location: " . SITE_URI . "facilities/locations?NOTICE=Location Deleted"));
			else
				$faSystemErrors[] = "Failed To Delete Location";
		}
		else
			throw new AppException("Location Is Invalid", "P04");
	}
	else
		throw new AppException("Location Not Defined", "P03");
