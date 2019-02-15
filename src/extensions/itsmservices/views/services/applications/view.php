<?php
	use itsmcore as itsmcore;
	use itsmwebmanager as itsmwebmanager;
	use itsmservices as itsmservices;
	
	if(!isset($_GET['a']))
		throw new AppException("Application Not Defined", "P03");
	
	$application = new itsmservices\Application();
	
	if(!$application->loadFromNumber($_GET['a']))
		throw new AppException("Application Is Invalid", "P04");
	
	$owner = new User($application->getOwner());
	$owner->load();
	
	$type = new Attribute($application->getApplicationType());
	$type->load();
	
	$le = new Attribute($application->getLifeExpectancy());
	$le->load();
	
	$at = new Attribute($application->getAuthType());
	$at->load();
	
	$dv = new Attribute($application->getDataVolume());
	$dv->load();
	
	$lastUpdate = $application->getLastUpdate();
	$lastUser = new User($lastUpdate->getUser());
	$lastUser->load();
	$lastStatus = new Attribute($lastUpdate->getStatus());
	$lastStatus->load();
	
	// Development History
	
	$history['type'] = "table";
	$history['head'] = ['Status', 'Time', 'Description', 'User'];
	$history['widths'] = ["150px", "150px", "", "200px"];
	$history['align'] = ["left", "left", "left", "right"];
	$history['data'] = [];
	
	foreach($application->getUpdates() as $update)
	{
		$status = new Attribute($update->getStatus());
		$status->load();
		$user = new User($update->getUser());
		$user->load();
		
		$history['data'][] = [$status->getName(), $update->getTime(), $update->getDescription(), $user->getFirstName() . " " . $user->getLastName() . " (" . $user->getUsername() . ")"];
	}
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../edit?a=<?=$application->getNumber()?>" accesskey="e">Edit</a>
	<a class="button" href="<?=getURI()?>/../update?a=<?=$application->getNumber()?>" accesskey="u">Update</a>
	<a class="button" href="<?=getURI()?>/../new?new" accesskey="c">Create</a>
</div>
<h2 class="region-title">Application Profile #<?=$application->getNumber()?></h2>
<table class="table-display application-display">
	<tr>
		<td>Name</td>
		<td><?=htmlentities($application->getName())?></td>
		<td>Owner</td>
		<td><?=$owner->getFirstName() . " " . $owner->getLastName() . " (" . $owner->getUsername() . ")"?></td>
	</tr>
	<tr>
		<td>Application Type</td>
		<td><?=$type->getName()?></td>
		<td>Life Expectancy</td>
		<td><?=$le->getName()?></td>
	</tr>
	<tr>
		<td>Authentication Type</td>
		<td><?=htmlentities($at->getName())?></td>
		<td>App Host Server(s)</td>
		<td>
			<ul>
			<?php
				foreach($application->getHosts('apph') as $host)
				{
					?>
					<li><a href="<?=SITE_URI?>devices/hosts/view?h=<?=$host->getId()?>"><?=htmlentities($host->getSystemName()) . " (" . $host->getIpAddress() . ")"?></a></li>
					<?php
				}
			?>
			</ul>
		</td>
	</tr>
	<tr>
		<td>Description</td>
		<td colspan=3><textarea class="textarea-display"><?=htmlentities($application->getDescription())?></textarea></td>
	</tr>
</table>
<h2 class="region-title">Web Details</h2>
<table class="table-display application-display">
	<tr>
		<td>Public Facing</td>
		<td><?=($application->getPublicFacing() == 1) ? "Yes" : "No"?></td>
		<td>Port</td>
		<td><?=htmlentities($application->getPort())?></td>
	</tr>
	<tr>
		<td>Web Host Server(s)</td>
		<td>
			<ul>
			<?php
				foreach($application->getHosts('webh') as $host)
				{
					?>
					<li><a href="<?=SITE_URI?>devices/hosts/view?h=<?=$host->getId()?>"><?=htmlentities($host->getSystemName()) . " (" . $host->getIpAddress() . ")"?></a></li>
					<?php
				}
			?>
			</ul>
		</td>
		<td>VHost(s)</td>
		<td>
			<ul>
			<?php
				foreach($application->getVHosts() as $vhost)
				{
					?>
					<li><a href="<?=SITE_URI?>webmanager/vhosts/view?v=<?=$vhost->getId()?>"><?=htmlentities($vhost->getSubdomain()) . "." . htmlentities($vhost->getDomain())?></a></li>
					<?php
				}
			?>
			</ul>
		</td>
	</tr>
</table>
<h2 class="region-title">Data Details</h2>
<table class="table-display application-display">
	<tr>
		<td>Data Host Server(s)</td>
		<td>
			<ul>
			<?php
				foreach($application->getHosts('data') as $host)
				{
					?>
					<li><a href="<?=SITE_URI?>devices/hosts/view?h=<?=$host->getId()?>"><?=htmlentities($host->getSystemName()) . " (" . htmlentities($host->getIpAddress()) . ")"?></a></li>
					<?php
				}
			?>
			</ul>
		</td>
		<td>Data Volume</td>
		<td><?=htmlentities($dv->getName())?></td>
	</tr>
</table>
<h2 class="region-title">Development Cycle</h2>
<table class="table-display application-display">
	<tr>
		<td>Status</td>
		<td><?=htmlentities($lastStatus->getName())?></td>
		<td>Entered On</td>
		<td><?=$lastUpdate->getTime()?></td>
	</tr>
	<tr>
		<td>By</td>
		<td><?=$lastUser->getFirstName() . " " . $lastUser->getLastName() . " (" . $lastUser->getUsername() . ")"?></td>
	</tr>
	<tr>
		<td>Description</td>
		<td colspan=3><textarea class="textarea-display"><?=htmlentities($lastUpdate->getDescription())?></textarea></td>
	</tr>
</table>
<h2 class="region-title region-expand region-expand-collapsed" id="devHistory">Development History</h2>
<div class="region" id="devHistory-region">	
	<span class="red-message">NO DATA FOUND</span>
</div>
<?php
	if(isset($history) AND !empty($history['data']))
	{
		?>
		<script>showResults('devHistory-region', <?=json_encode($history)?>, <?=RESULTS_PER_PAGE?>)</script>
		<?php
	}
?>