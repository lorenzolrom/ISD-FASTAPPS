<?php
	use itsmservicemonitor as sm;
	
	if(!isset($_GET['c']))
		throw new AppException("Category Not Defined", "P03");
	
	$category = new sm\ApplicationCategory($_GET['c']);
	if(!$category->load())
		throw new AppException("Category Not Found", "P04");
	
	if($category->delete())
		exit(header("Location: " . SITE_URI . "monitor/services/categories?NOTICE=Category Deleted"));
	else
		$faSystemErrors[] = "Could Not Delete Category";
