<?php
	use itsmwebmanager as itsmwebmanager;
	
	$results = [];
	
	if(isset($_GET['registrar']))
	{
		$registrar = new itsmwebmanager\Registrar();
		
		if($registrar->loadFromCode($_GET['registrar']))
		{
			$registrarAttributes['name'] = $registrar->getName();
			
			$results = $registrarAttributes;
		}
	}
	else if(isset($_GET['registrarCodes']))
	{
		$registrars = itsmwebmanager\getRegistrars();
		
		$registrarCodes = [];
		
		foreach($registrars as $registrar)
		{
			$registrarCodes[] = $registrar->getCode();
		}
		
		$results = $registrarCodes;
	}
	
	$results = json_encode($results, JSON_HEX_QUOT);
?>
<div id="encoded-data">
	<?=ifSet($results)?>
</div>