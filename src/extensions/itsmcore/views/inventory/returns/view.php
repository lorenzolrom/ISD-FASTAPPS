<?php
	use itsmcore as itsmcore;
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			$vendor = new itsmcore\Vendor($returnOrder->getVendor());
			$warehouse = new itsmcore\Warehouse($returnOrder->getWarehouse());
			$lastModifyUser = new User($returnOrder->getLastModifyUser());
			$createUser = new User($returnOrder->getCreateUser());
			$status = new Attribute($returnOrder->getStatus());
			$type = new Attribute($returnOrder->getReturnType());
			
			$vendor->load();
			$warehouse->load();
			$lastModifyUser->load();
			$createUser->load();
			$status->load();
			$type->load();
			
			// List assets
			$assets = $returnOrder->getAssets();
			
			$assetResults['type'] = "table";
			$assetResults['linkColumn'] = 4;
			$assetResults['href'] = SITE_URI . "inventory/returns/assets?POPUP=yes&function=remove&ro=" . $returnOrder->getNumber() . "&id=";
			$assetResults['head'] = ['Asset Tag', 'Name', 'Location', 'Warehouse', ''];
			$assetResults['refs'] = [];
			$assetResults['data'] = [];
			$assetResults['classes'] = [];
			$assetResults['widths'] = ["100px", "", "100px", "100px", "10px"];
			$assetResults['align'] = ["right", "left", "left", "left", "center"];
			
			foreach($assets as $asset)
			{
				$assetLocation = new facilitiescore\Location($asset->getLocation());
				$assetLocation->load();
				$assetBuilding = new facilitiescore\Building($assetLocation->getBuilding());
				$assetBuilding->load();
				$assetWarehouse = new itsmcore\Warehouse($asset->getWarehouse());
				$assetWarehouse->load();
				$commodity = new itsmcore\Commodity($asset->getCommodity());
				$commodity->load();
				
				$assetResults['refs'][] = $asset->getId();
				$assetResults['classes'] = ["", "", "", "", "itsmcore-small-popup-link-td"];
				$assetResults['data'][] = [$asset->getAssetTag(), $commodity->getName(), ($asset->getLocation() !== NULL ? ($assetBuilding->getCode() . " " . $assetLocation->getCode()) : ""), ($assetWarehouse->getCode() !== NULL ? $assetWarehouse->getCode() : ""), 'REMOVE'];
			}
			
			// List cost items
			$costItems = $returnOrder->getCostItems();
			
			$costItemResults['type'] = "table";
			$costItemResults['linkColumn'] = 2;
			$costItemResults['href'] = SITE_URI . "inventory/returns/costitems?POPUP=yes&function=remove&ro=" . $returnOrder->getNumber() . "&id=";
			$costItemResults['head'] = ['Cost', 'Notes', ''];
			$costItemResults['data'] = [];
			$costItemResults['classes'] = [];
			$costItemResults['widths'] = ["150px", "", "10px"];
			$costItemResults['align'] = ["right", "left", "left"];
			
			foreach($costItems as $costItem)
			{
				$costItemResults['refs'][] = $costItem['id'];
				$costItemResults['data'][] = [$costItem['cost'], $costItem['notes'], 'REMOVE'];
				$costItemResults['classes'] = ["", "", "itsmcore-small-popup-link-td"];
			}
			
			?>
			<div class="button-bar">
				<?php
				if($returnOrder->getSent() == 0) // Order not sent
				{
					?><a class="button" href="<?=getURI()?>/../edit?ro=<?=$returnOrder->getNumber()?>" accesskey="e">Edit</a>
					<?php
						if(sizeof($returnOrder->getAssets()) > 0) // Order has assets
						{
						?><a class="button confirm-button" href="<?=getURI()?>/../process?function=send&ro=<?=$returnOrder->getNumber()?>" accesskey="s">Send</a>
						<?php
						}
					?><a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?ro=<?=$returnOrder->getNumber()?>" accesskey="d">Delete</a>
					<?php
				}
				else if($returnOrder->getSent() == 1 AND $returnOrder->getReceived() == 0 AND $returnOrder->getCanceled() == 0) // Order sent but not received or canceled
				{
					?><a class="button" href="<?=getURI()?>/../process?function=receive&ro=<?=$returnOrder->getNumber()?>" accesskey="r">Receive</a>
					<a class="button confirm-button" href="<?=getURI()?>/../process?function=cancel&ro=<?=$returnOrder->getNumber()?>" accesskey="a">Cancel</a>
					<?php
				}
				?><a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Return Order Profile</h2>
			<table class="table-display returnorder-display">
				<tr>
					<td>R.O. #</td>
					<td><?=$returnOrder->getNumber()?></td>
					<td>Vendor RMA #</td>
					<td><?=htmlentities($returnOrder->getVendorRMA())?></td>
				</tr>
				<tr>
					<td>Type</td>
					<td><?=htmlentities($type->getName())?></td>
					<td>Status</td>
					<td><?=htmlentities($status->getName())?></td>
				</tr>
			</table>
			<h2 class="region-title">Order Details</h2>
			<table class="table-display returnorder-display">
				<tr>
					<td>Vendor Code</td>
					<td><a href="<?=SITE_URI . "inventory/settings/vendors/view?v=" . $vendor->getId()?>"><?=htmlentities($vendor->getCode())?></a></td>
					<td>Vendor Name</td>
					<td><?=htmlentities($vendor->getName())?></td>
				</tr>
				<tr>
					<td>Warehouse Code</td>
					<td><a href="<?=SITE_URI . "inventory/settings/warehouses/view?w=" . $warehouse->getId()?>"><?=htmlentities($warehouse->getCode())?></a></td>
					<td>Warehouse Name</td>
					<td><?=htmlentities($warehouse->getName())?></td>
				</tr>
				<tr>
					<td>Order Date</td>
					<td><?=$returnOrder->getOrderDate()?></td>
				</tr>
				<tr>
					<td>Sent</td>
					<td><?=($returnOrder->getSent() == 1) ? "Yes" : "No"?></td>
					<td>Send Date</td>
					<td><?=$returnOrder->getSendDate()?></td>
				</tr>
				<tr>
					<td>Received</td>
					<td><?=($returnOrder->getReceived() == 1) ? "Yes" : "No"?></td>
					<td>Receive Date</td>
					<td><?=$returnOrder->getReceiveDate()?></td>
				</tr>
				<tr>
					<td>Canceled</td>
					<td><?=($returnOrder->getCanceled() == 1) ? "Yes" : "No"?></td>
					<td>Cancel Date</td>
					<td><?=$returnOrder->getCancelDate()?></td>
				</tr>
			</table>
			<h2 class="region-title">Assets<?php
			if($returnOrder->getSent() == 0)
			{
				?>
				<a href="<?=getURI()?>/../assets?POPUP=yes&function=add&ro=<?=$returnOrder->getNumber()?>" class="button-noveil itsmcore-small-popup-link" accesskey="o">Add Asset</a>
				<?php
			}
			?></h2>
			<div id="assetResults">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<h2 class="region-title">Cost Items<?php
			if($returnOrder->getSent() == 0)
			{
				?>
				<a href="<?=getURI()?>/../costitems?POPUP=yes&function=add&ro=<?=$returnOrder->getNumber()?>" class="button-noveil itsmcore-small-popup-link" accesskey="i">Add Cost Item</a>
				<?php
			}
			?></h2>
			<div id="costItemResults">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display returnorder-display">
				<tr>
					<td>Notes</td>
					<td colspan=3><textarea readonly class="textarea-display"><?=htmlentities($returnOrder->getNotes())?></textarea></td>
				</tr>
			</table>
			<table class="table-display returnorder-display">
				<tr>
					<td>Created</td>
					<td><?=$returnOrder->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$returnOrder->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
			if(isset($assetResults) AND !empty($assetResults['data']))
			{
				?>
				<script>showResults('assetResults', <?=json_encode($assetResults)?>, 5)</script>
				<?php
			}
			if(isset($costItemResults) AND !empty($costItemResults['data']))
			{
				?>
				<script>showResults('costItemResults', <?=json_encode($costItemResults)?>, 5)</script>
				<?php
			}
			?>
			<script>
				ITSMCore_addPopupListeners();
			</script>
			<?php
		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
?>