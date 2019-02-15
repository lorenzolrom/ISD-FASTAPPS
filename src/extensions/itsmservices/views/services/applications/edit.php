<?php
	use itsmcore as itsmcore;
	use itsmwebmanager as itsmwebmanager;
	use itsmservices as itsmservices;
	
	$application = null;
	
	if(!isset($_GET['new'])) // If not creating a new app, load specified one
	{
		if(!isset($_GET['a'])) // Is app set?
			throw new AppException("Application Not Defined", "P03");
		
		$application = new itsmservices\Application();
		
		if(!$application->loadFromNumber($_GET['a'])) // Does app # exist? 
			throw new AppException("Application Is Invalid", "P04");
	}
	
	if(!empty($_POST))
	{
		if(isset($_GET['new']))
		{
			$application = new itsmservices\Application();
			$create = $application->create($_POST);
			
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Application Created";
			else
				$faSystemErrors[] = "Could Not Create Application";
		}
		else
		{
			$save = $application->save($_POST);
			
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Changes Saved";
			else
				$faSystemErrors[] = "Could Not Save Application";
		}
			
		if(isset($notice))
			exit(header("Location: " . SITE_URI . "services/applications/view?a=" . $application->getNumber() . "&NOTICE=" . $notice));
	}
	
	if($application !== NULL AND !isset($_GET['new'])) // If all is not set, load details into POST
	{
		// Details
		$_POST['name'] = $application->getName();
		$_POST['applicationType'] = $application->getApplicationType();
		$_POST['lifeExpectancy'] = $application->getLifeExpectancy();
		$_POST['authType'] = $application->getAuthType();
		$_POST['description'] = $application->getDescription();
		$_POST['publicFacing'] = $application->getPublicFacing();
		$_POST['dataVolume'] = $application->getDataVolume();
		$_POST['port'] = $application->getPort();
		
		// Owner (username)
		$owner = new User($application->getOwner());
		$owner->load();
		$_POST['ownerUsername'] = $owner->getUsername();
		
		// App Hosts
		
		foreach($application->getHosts('apph') as $host)
		{
			$_POST['appHosts'][] = $host->getId();
		}
		
		// Web Hosts
		
		foreach($application->getHosts('webh') as $host)
		{
			$_POST['webHosts'][] = $host->getId();
		}
		
		// Data Hosts
		
		foreach($application->getHosts('data') as $host)
		{
			$_POST['dataHosts'][] = $host->getId();
		}
		
		// VHosts
		
		foreach($application->getVHosts() as $vhost)
		{
			$_POST['vhosts'][] = $vhost->getId();
		}
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="application" accesskey="s">Save</span>
		<?php
			if(!isset($_GET['new']))
			{
				?>
				<a class="button" href="<?=SITE_URI?>services/applications/view?a=<?=$application->getNumber()?>" accesskey="c">Cancel</a>
				<?php
			}
			else
			{
				?>
				<span class="button back-button" accesskey="c">Cancel</span>
				<?php
			}
		?>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/applicationform.php");
?>