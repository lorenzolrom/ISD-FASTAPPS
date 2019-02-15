<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['a']))
	{
		$asset = new itsmcore\Asset($_GET['a']);
		
		if($asset->load())
		{
			if($_GET['function'] == "verify")
			{
				if($asset->verify())
				{
					header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Asset Verified");
					exit();
				}
			}
			else if($_GET['function'] == "unverify")
			{
				if($asset->unverify())
				{
					header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Asset Un-Verified");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Verify Asset";
			}
			else
				throw new AppException("Function Is Invalid", "P04");
		}
		else
			throw new AppException("Asset Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Not Defined", "P03");
