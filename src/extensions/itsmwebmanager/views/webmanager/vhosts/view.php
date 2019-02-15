<?php
	use itsmwebmanager as itsmwebmanager;
	use itsmcore as itsmcore;
	
	if(isset($_GET['v']))
	{
		$vhost = new itsmwebmanager\VHost($_GET['v']);
		
		if($vhost->load())
		{
			$lastModifyUser = new User($vhost->getLastModifyUser());
			$createUser = new User($vhost->getCreateUser());
			$status = new Attribute($vhost->getStatus());
			$host = new itsmcore\Host($vhost->getHost());
			$registrar = new itsmwebmanager\Registrar($vhost->getRegistrar());
			
			$lastModifyUser->load();
			$createUser->load();
			$status->load();
			$host->load();
			$registrar->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?v=<?=$vhost->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?v=<?=$vhost->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">VHost Profile</h2>
			<table class="table-display vhost-display">
				<tr>
					<td>Subdomain</td>
					<td><?=htmlentities($vhost->getSubdomain())?></td>
					<td>Domain</td>
					<td><?=htmlentities($vhost->getDomain())?></td>
				</tr>
				<tr>
					<td>Name</td>
					<td><?=htmlentities($vhost->getName())?></td>
					<td>Renew Cost</td>
					<td><?=$vhost->getRenewCost()?></td>
				</tr>
				<tr>
					<td>Host</td>
					<td><a href="<?=SITE_URI?>devices/hosts/view?h=<?=$host->getId()?>"><?=htmlentities($host->getSystemName()) . " (" . htmlentities($host->getIpAddress()) . ")"?></a></td>
					<td>Status</td>
					<td><?=htmlentities($status->getName())?></td>
				</tr>
				<tr>
					<td>Date Registered</td>
					<td><?=$vhost->getRegisterDate()?></td>
					<td>Date Expires</td>
					<td><?=$vhost->getExpireDate()?></td>
				</tr>
			</table>
			<h2 class="region-title">Registrar Details</h2>
			<table class="table-display vhost-display">
				<tr>
					<td>Registrar Code</td>
					<td><a href="<?=SITE_URI?>webmanager/registrars/view?r=<?=$registrar->getId()?>"><?=htmlentities($registrar->getCode())?></a></td>
					<td>Registrar Name</td>
					<td><?=htmlentities($registrar->getName())?></td>
				<tr>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display vhost-display">
				<tr>
					<td>Notes</td>
					<td><textarea colspan=3 readonly class="textarea-display"><?=htmlentities($vhost->getNotes())?></textarea></td>
				</tr>
				<tr>
					<td>Created</td>
					<td><?=$vhost->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$vhost->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("VHost Is Invalid", "P04");
	}
	else
		throw new AppException("VHost Not Defined", "P03");
?>