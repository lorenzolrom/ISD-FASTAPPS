<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['c']))
	{
		$commodity = new itsmcore\Commodity($_GET['c']);
		
		if($commodity->load())
		{
			if($commodity->delete())
			{
				exit(header("Location: " . SITE_URI . "inventory/commodities?NOTICE=Commodity Deleted"));
			}
			else
				$faSystemErrors[] = "Could Not Delete Commodity";
		}
		else
			throw new AppException("Commodity Is Invalid", "P04");
	}
	else
		throw new AppException("Commodity Not Defined", "P03");
