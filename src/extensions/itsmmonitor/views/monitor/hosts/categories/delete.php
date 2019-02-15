<?php
	use itsmmonitor as itsmmonitor;
	
	if(!isset($_GET['c']))
		throw new AppException("Category Not Defined", "P03");
	
	$category = new itsmmonitor\HostCategory($_GET['c']);
	if(!$category->load())
		throw new AppException("Category Not Found", "P04");
	
	if($category->delete())
		exit(header("Location: " . SITE_URI . "monitor/hosts/categories?NOTICE=Category Deleted"));
	else
		$faSystemErrors[] = "Could Not Delete Category";
