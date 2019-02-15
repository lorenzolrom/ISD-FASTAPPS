<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['h']))
	{
		$host = new itsmcore\Host($_GET['h']);
		
		if($host->load())
		{
			$asset = new itsmcore\Asset($host->getAsset());
			$asset->load();
			
			$commodity = new itsmcore\Commodity($asset->getCommodity());
			$commodity->load();
			
			$createUser = new User($host->getCreateUser());
			$createUser->load();
			
			$lastModifyUser = new User($host->getLastModifyUser());
			$lastModifyUser->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?h=<?=$host->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?h=<?=$host->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Host Profile</h2>
			<table class="table-display host-display">
				<tr>
					<td>IP Address</td>
					<td><?=$host->getIpAddress()?></td>
					<td>MAC Address</td>
					<td><?=$host->getMacAddress()?></td>
				</tr>
				<tr>
					<td>Asset Tag</td>
					<td><a href="<?=SITE_URI . "inventory/assets/view?a=" . $asset->getId()?>"><?=$asset->getAssetTag()?></a></td>
				</tr>
			</table>
			<h2 class="region-title">System Information</h2>
			<table class="table-display host-display">
				<tr>
					<td>System Name</td>
					<td><?=$host->getSystemName()?></td>
					<td>System CPU</td>
					<td><?=$host->getSystemCPU()?></td>
				</tr>
				<tr>
					<td>System RAM</td>
					<td><?=$host->getSystemRAM()?></td>
					<td>System OS</td>
					<td><?=$host->getSystemOS()?></td>
				</tr>
				<tr>
					<td>System Domain</td>
					<td><?=$host->getSystemDomain()?></td>
				</tr>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$host->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$host->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Host Is Invalid", "P04");
	}
	else
		throw new AppException("Host Not Defined", "P03");
?>