<?php
	if(isset($_GET['r']))
	{
		$deleteRole = new Role($_GET['r']);
		
		if($deleteRole->load())
		{
			if($deleteRole->delete())
			{
				exit(header("Location: " . SITE_URI . "settings/roles?NOTICE=Role Deleted"));
			}
			else
				$faSystemErrors[] = "Could Not Delete Role";
		}
		else
			throw new AppException("Role Is Invalid", "P04");
	}
	else
		throw new AppException("Role Not Defined", "P03");
