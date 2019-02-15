<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['v']))
	{
		$vendor = new itsmcore\Vendor($_GET['v']);
		
		if($vendor->load())
		{
			if($vendor->delete())
			{
				exit(header("Location: " . SITE_URI . "inventory/settings/vendors?NOTICE=Vendor Deleted"));
			}
			else
				$faSystemErrors[] = "Could Not Delete Vendor";
		}
		else
			throw new AppException("Vendor Is Invalid", "P04");
	}
	else
		throw new AppException("Vendor Not Defined", "P03");
