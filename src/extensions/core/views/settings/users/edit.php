<?php
	if(isset($_GET['u']))
	{
		$user = new User($_GET['u']);
		
		if($user->load())
		{
			if(!empty($_POST))
			{
				$save = $user->save($_POST);
				
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "settings/users?NOTICE=Changed Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			// Load values from user
			$_POST['username'] = $user->getUsername();
			$_POST['firstName'] = $user->getFirstName();
			$_POST['lastName'] = $user->getLastName();
			$_POST['email'] = $user->getEmail();
			$_POST['disabled'] = $user->getDisabled();
			$_POST['authType'] = $user->getAuthType();
			$_POST['roles'] = [];
			foreach($user->getRoles() as $role)
			{
				$_POST['roles'][] = $role->getId();
			}
			
			require_once(dirname(__FILE__) . "/userform.php");
		}
		else
			throw new AppException("User Is Invalid", "P04");
	}
	else
		throw new AppException("User Not Defined", "P03");
