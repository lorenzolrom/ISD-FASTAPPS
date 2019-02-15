<?php
	use itsmwebmanager as itsmwebmanager;
	
	if(isset($_GET['r']))
	{
		$registrar = new itsmwebmanager\Registrar($_GET['r']);
		
		if($registrar->load())
		{
			if($registrar->delete())
			{
				exit(header("Location: " . SITE_URI . "webmanager/registrars?NOTICE=Registrar Deleted"));
			}
			else
				$faSystemErrors[] = "Failed To Delete Registrar";
		}
		else
			throw new AppException("Registrar Is Invalid", "P04");
	}
	else
		throw new AppException("Registrar Not Defined", "P03");
