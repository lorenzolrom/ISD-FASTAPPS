<?php
	if(isset($_GET['t']))
	{
		$attribute = new Attribute($_GET['t']);
		
		if($attribute->load() AND $attribute->getExtension() == "itsm" AND $attribute->getAttributeType() == "asty")
		{
			if($attribute->delete())
			{
				header("Location: " . SITE_URI . "inventory/settings/assettypes?NOTICE=Asset Type Deleted");
				exit();
			}
			else
				$faSystemErrors[] = "Failed To Delete Asset Type";
		}
		else
			throw new AppException("Asset Type Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Type Not Defined", "P03");
