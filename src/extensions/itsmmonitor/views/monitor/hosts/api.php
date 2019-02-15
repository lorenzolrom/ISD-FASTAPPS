<?php
	use itsmmonitor as monitor;
	use itsmcore as core;
	
	$results = [];
	
	if(isset($_GET['categories'])) // Get all categories
	{		
		$categories = monitor\getCategories(TRUE); // Get all displayed categories
		
		foreach($categories as $category)
		{
			// Add category name
			$categoryArray = [];
			$categoryArray['name'] = $category->getName();
			$categoryArray['offlineCount'] = 0;
			$categoryArray['hosts'] = [];
			
			foreach($category->getHosts() as $host)
			{
				$hostArray = [];
				$hostArray['name'] = $host->getSystemName();
				
				if(!$host->isOnline())
				{
					$hostArray['online'] = "0";
					$categoryArray['offlineCount'] += 1;
				}
				else
				{
					$hostArray['online'] = "1";
				}
				
				$categoryArray['hosts'][] = $hostArray;
			}
			
			$results[] = $categoryArray;
		}
	}
	else if(isset($_GET['category'])) // Get details for a specific category
	{
		$category = new monitor\HostCategory($_GET['category']);
		if($category->load())
		{
			$results['name'] = $category->getName();
			$results['offlineCount'] = 0;
			$results['hosts'] = [];
			
			foreach($category->getHosts() as $host)
			{
				$hostArray = [];
				$hostArray['name'] = $host->getSystemName();
				
				if(!$host->isOnline())
				{
					$hostArray['online'] = "0";
					$results['offlineCount'] += 1;
				}
				else
					$hostArray['online'] = "1";
				
				$results['hosts'][] = $hostArray;
			}
			
			if($results['offlineCount'] == sizeof($results['hosts'])) // All hosts offline
				$results['indicator'] = "red-blink.gif";
			else if($results['offlineCount'] > 0)
				$results['indicator'] = "yellow-blink.gif";
			else
				$results['indicator'] = "green-solid.gif";
		}
	}
	
	$results = json_encode($results, JSON_HEX_QUOT);
?>
<div id="encoded-data">
	<?=ifSet($results)?>
</div>