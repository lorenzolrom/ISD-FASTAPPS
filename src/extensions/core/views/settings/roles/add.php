<?php
	if(!empty($_POST))
	{
		$role = new Role();
		$create = $role->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "settings/roles?NOTICE=Role Created"));
		else
			$faSystemErrors[] = "Could Not Create Role";
	}
	
	require_once(dirname(__FILE__) . "/roleform.php");
