<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['a']))
	{
		$asset = new itsmcore\Asset($_GET['a']);
		
		if($asset->load())
		{
			if(!empty($asset->getHosts()))
			{
				header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Cannot Discard - Asset Has Hosts");
				exit();
			}
			
			if($asset->getReturnOrder() !== FALSE)
			{
				header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Cannot Discard - Asset Is On Return Order");
				exit();
			}
			
			if($asset->discard())
			{
				header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Asset Discarded");
				exit();
			}
			else
				$faSystemErrors[] = "Failed To Discard Asset";
		}
		else
			throw new AppException("Asset Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Not Defined", "P03");
