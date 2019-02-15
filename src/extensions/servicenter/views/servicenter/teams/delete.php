<?php
	use servicenter as sc;
	
	if(!isset($_GET['t']))
		throw new AppException("Team Not Defined", "P03");
	
	$team =  new sc\Team($_GET['t']);
	if(!$team->load())
		throw new AppException("Team Is Invalid", "P04");
	
	if($team->delete())
		exit(header("Location: " . SITE_URI . "servicenter/teams?NOTICE=Team Deleted"));
	else
		$faSystemErrors[] = "Failed To Delete Team";
