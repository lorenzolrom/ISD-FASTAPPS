<?php
	use itsmwebmanager as itsmwebmanager;
	use itsmcore as itsmcore;
	
	if(!isset($_GET['v']))
		throw new AppException("VHost Not Defined", "P03");
	
	$vhost = new itsmwebmanager\VHost($_GET['v']);
		
	if(!$vhost->load())
		throw new AppException();
	
	if($vhost->delete())
		exit(header("Location: " . SITE_URI . "webmanager/vhosts?NOTICE=VHost Deleted"));
	else
		$faSystemErrors[] = "Failed To Delete VHost";
