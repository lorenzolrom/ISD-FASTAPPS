<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['b']))
	{
		$building = new facilitiescore\Building($_GET['b']);
		
		if($building->load())
		{			
			if($building->delete())
				exit(header("Location: " . SITE_URI . "facilities/buildings?NOTICE=Building Deleted"));
			else
				$faSystemErrors[] = "Could Not Delete Building";
		}
		else
		{
			throw new AppException("Building Is Invalid", "P04");
		}
	}
	else
	{
		throw new AppException("Building Not Defined", "P03");
	}
