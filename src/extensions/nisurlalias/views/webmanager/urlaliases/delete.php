<?php
	use nisurlalias as ua;
	
	if(!isset($_GET['a']))
		throw new AppException("Alias Not Defined", "P03");
	
	$alias = new ua\URLAlias($_GET['a']);
	if(!$alias->load())
		throw new AppException("Alias Not Found", "P04");
	
	if($alias->delete())
		exit(header("Location: " . SITE_URI . "webmanager/urlaliases?NOTICE=Alias Deleted"));
	else
		$faSystemErrors[] = "Could Not Delete Alias";
