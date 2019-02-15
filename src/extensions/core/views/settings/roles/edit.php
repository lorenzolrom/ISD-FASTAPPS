<?php
	if(isset($_GET['r']))
	{
		$role = new Role($_GET['r']);
		
		if($role->load())
		{
			if(!empty($_POST))
			{
				$save = $role->save($_POST);
				
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "settings/roles?NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			// Load values from role
			$_POST['name'] = $role->getName();
			$_POST['permissions'] = $role->getPermissions();
			
			require_once(dirname(__FILE__) . "/roleform.php");
			?>
			<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?r=<?=$role->getId()?>">Delete</a>
			<?php
		}
		else
			throw new AppException("Role Is Invalid", "P04");
	}
	else
		throw new AppException("Role Not Defined", "P03");
?>