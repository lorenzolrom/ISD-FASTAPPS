<?php
	use itsmservicemonitor as sm;
	use itsmservices as services;
	
	$results = [];
	
	if(isset($_GET['category']))
	{
		$category = new sm\ApplicationCategory($_GET['category']);
		if($category->load())
		{
			$results['name'] = $category->getName();
			$results['indicator'] = "green-solid.gif";
			$results['troubleCount'] = 0;
			$results['applications'] = [];
			
			foreach($category->getApplications() as $application)
			{
				$appArray = [];
				$appArray['number'] = $application->getNumber();
				$appArray['name'] = $application->getName();
				
				// Host Type Status Indicators (1 = OK, 0 = TROUBLE)
				$appArray['apph'] = 1;
				$appArray['webh'] = 1;
				$appArray['data'] = 1;
				
				// Online Status For All Hosts
				$hostTypes = ['apph', 'webh', 'data'];
				
				foreach($hostTypes as $type)
				{
					$hosts = $application->getHosts($type);
					
					foreach($hosts as $host)
					{
						if(!$host->isOnline())
						{
							$appArray[$type] = 0;
						}
					}
				}
				
				if($appArray['apph'] == 0 OR $appArray['webh'] == 0 OR $appArray['data'] == 0)
				{
					$results['indicator'] = "yellow-blink.gif";
					$results['troubleCount'] += 1;
				}
				
				if($appArray['apph'] == 0 AND $appArray['webh'] == 0 AND $appArray['data'] == 0)
					$results['indicator'] = "red-blink.gif";
				
				$results['applications'][] = $appArray;
			}
		}
	}
	
	$results = json_encode($results, JSON_HEX_QUOT);
?>
<div id="encoded-data">
	<?=ifSet($results)?>
</div>