<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['h']))
	{
		$host = new itsmcore\Host($_GET['h']);
		
		if($host->load())
		{
			if($host->delete())
			{
				exit(header("Location: " . SITE_URI . "devices/hosts?NOTICE=Host Deleted"));
			}
			else
				$faSystemErrors[] = "Failed To Delete Host";
		}
		else
			throw new AppException("Host Is Invalid", "P04");
	}
	else
		throw new AppException("Host Not Defined", "P03");
