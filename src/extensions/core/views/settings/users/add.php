<?php
	if(!empty($_POST))
	{
		$user = new User();
		
		$create = $user->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "settings/users?NOTICE=User Added"));
		else
			$faSystemErrors[] = "User Could Not Be Added";
	}
	
	require_once(dirname(__FILE__) . "/userform.php");
