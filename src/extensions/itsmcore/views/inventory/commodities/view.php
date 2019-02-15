<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['c']))
	{		
		$commodity = new itsmcore\Commodity($_GET['c']);
		
		if($commodity->load())
		{
			$lastModifyUser = new User($commodity->getLastModifyUser());
			$lastModifyUser->load();
			
			$createUser = new User($commodity->getCreateUser());
			$createUser->load();
			
			$commodityType = new Attribute($commodity->getCommodityType());
			$commodityType->load();
			
			$assetType = new Attribute($commodity->getAssetType());
			$assetType->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?c=<?=$commodity->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?c=<?=$commodity->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Commodity Profile</h2>
			<table class="table-display commodity-display">
				<tr>
					<td>Commodity Code</td>
					<td><?=htmlentities($commodity->getCode())?></td>
					<td>Commodity Name</td>
					<td><?=htmlentities($commodity->getName())?></td>
				</tr>
				<tr>
					<td>Commodity Type</td>
					<td><?=htmlentities($commodityType->getName())?></td>
					<td>Asset Type</td>
					<td><?=htmlentities($assetType->getName())?></td>
				</tr>
				<tr>
					<td>Manufacturer</td>
					<td><?=htmlentities($commodity->getManufacturer())?></td>
					<td>Model</td>
					<td><?=htmlentities($commodity->getModel())?></td>
				</tr>
			</table>
			<h2 class="region-title">Financial Information</h2>
			<table class="table-display commodity-display">
				<tr>
					<td>Unit Cost</td>
					<td><?=$commodity->getUnitCost()?></td>
				</tr>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$commodity->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$commodity->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Commodity Is Invalid", "P04");
	}
	else
		throw new AppException("Commodity Not Defined", "P03");
?>