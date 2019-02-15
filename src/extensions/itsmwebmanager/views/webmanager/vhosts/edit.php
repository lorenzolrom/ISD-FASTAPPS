<?php
	use itsmwebmanager as itsmwebmanager;
	use itsmcore as itsmcore;
	
	if(!isset($_GET['v']))
		throw new AppException("VHost Not Defined", "P03");
	
	$vhost = new itsmwebmanager\VHost($_GET['v']);
		
	if(!$vhost->load())
		throw new AppException();
	
	$registrar = new itsmwebmanager\Registrar($vhost->getRegistrar());
	$registrar->load();
	
	$host = new itsmcore\Host($vhost->getHost());
	$host->load();
	
	if(!empty($_POST))
	{		
		$save = $vhost->save($_POST);
		if(is_array($save))
			$faSystemErrors = $save;
		else if($save == TRUE)
			exit(header("Location: " . SITE_URI . "webmanager/vhosts/view?v=" . $vhost->getId() . "&NOTICE=Changes Saved"));
		else
			$faSystemErrors[] = "Could Not Create VHost";
	}
	
	$_POST['subdomain'] = $vhost->getSubdomain();
	$_POST['domain'] = $vhost->getDomain();
	$_POST['name'] = $vhost->getName();
	$_POST['renewCost'] = $vhost->getRenewCost();
	$_POST['hostIp'] = $host->getIpAddress();
	$_POST['status'] = $vhost->getStatus();
	$_POST['registerDate'] = $vhost->getRegisterDate();
	$_POST['expireDate'] = $vhost->getExpireDate();
	$_POST['registrarCode'] = $registrar->getCode();
	$_POST['registrarName'] = $registrar->getName();
	$_POST['notes'] = $vhost->getNotes();
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="vhost" accesskey="s">Save</span>
		<a href="<?=SITE_URI?>webmanager/vhosts/view?v=<?=$vhost->getId()?>" class="button" accesskey="c">Cancel</a>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/vhostform.php");
?>