<?php
	use itsmcore as itsmcore;
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['a']))
	{
		$asset = new itsmcore\Asset($_GET['a']);
		
		if($asset->load())
		{
			// Parent
			if($asset->getParent() !== NULL)
			{
				$parent = new itsmcore\Asset($asset->getParent());
				$parent->load();
			}
			
			// Commodity
			$commodity = new itsmcore\Commodity($asset->getCommodity());
			$commodity->load();
			
			// Commodity Type
			$commodityType = new Attribute($commodity->getCommodityType());
			$commodityType->load();
			
			// Asset Type
			$assetType = new Attribute($commodity->getAssetType());
			$assetType->load();
			
			// Purchase Order
			$purchaseOrder = new itsmcore\PurchaseOrder($asset->getPurchaseOrder());
			$purchaseOrder->load();
			
			// Warehouse
			$warehouse = new itsmcore\Warehouse($asset->getWarehouse());
			$warehouse->load();
			
			// Location
			$location = new facilitiescore\Location($asset->getLocation());
			$location->load();
			
			// Building
			$building = new facilitiescore\Building($location->getBuilding());
			$building->load();
			
			// Last Modify User
			$lastModifyUser = new User($asset->getLastModifyUser());
			$lastModifyUser->load();
			
			// Worksheet
			$worksheet = new itsmcore\AssetWorksheet();
			
			// Generate Return History
			
			$returnHistory['type'] = "table";
			$returnHistory['linkColumn'] = [0];
			$returnHistory['href'] = [SITE_URI . "inventory/returns/view?ro="];
			$returnHistory['head'] = ['R.O. #', 'Date', 'Type', 'RMA #', 'Status', 'Vendor'];
			$returnHistory['widths'] = ["50px", "100px", "", "150px", "", ""];
			$returnHistory['align'] = ["center", "left", "left" , "left", "left", "left"];
			$returnHistory['data'] = [];
			$returnHistory['refs'] = [];
			
			foreach($asset->getReturnOrders() as $returnOrder)
			{
				$returnType = new Attribute($returnOrder->getReturnType());
				$returnType->load();
				$status = new Attribute($returnOrder->getStatus());
				$status->load();
				$vendor = new itsmcore\Vendor($returnOrder->getVendor());
				$vendor->load();
				
				$returnHistory['refs'][] = [$returnOrder->getNumber()];
				$returnHistory['data'][] = [$returnOrder->getNumber(), $returnOrder->getOrderDate(), 
					$returnType->getName(), $returnOrder->getVendorRMA(), $status->getName(), $vendor->getName()];
			}
			
			// Generate child assets
			
			$childAssets['type'] = "table";
			$childAssets['linkColumn'] = [0];
			$childAssets['href'] = ["view?a="];
			$childAssets['head'] = ['Asset #', 'Commodity Name'];
			$childAssets['widths'] = ["50px", ""];
			$childAssets['align'] = ["center", "left"];
			$childAssets['data'] = [];
			$childAssets['refs'] = [];
			
			foreach($asset->getChildren() as $child)
			{
				$childCommodity = new itsmcore\Commodity($child->getCommodity());
				$childCommodity->load();
				$childAssets['refs'][] = [$child->getId()];
				$childAssets['data'][] = [$child->getAssetTag(), $childCommodity->getName()];
			}
			
			?>
			<div class="button-bar">
				<?php
				if($asset->getDiscarded() == 0)
				{
				?>
					<a href="<?=SITE_URI?>inventory/assets/edit?a=<?=$asset->getId()?>" class="button" accesskey="e">Edit</a>
					<?php
						if($asset->getParent() == NULL)
						{
							?>
							<a href="<?=SITE_URI?>inventory/assets/link?a=<?=$asset->getId()?>&f=link" class="button" accesskey="l">Link To Parent</a>
							<?php
						}
						else
						{
							?>
							<a href="<?=SITE_URI?>inventory/assets/link?a=<?=$asset->getId()?>&f=unlink" class="button delete-button confirm-button" accesskey="u">Unlink From Parent</a>
							<?php
						}
					
						if($asset->getVerified() == 0)
						{
						?>
						<a class="button" href="<?=SITE_URI?>inventory/assets/verify?function=verify&a=<?=$asset->getId()?>" accesskey="v">Verify</a>
						<?php
						}
						else
						{
						?>
						<a class="button" href="<?=SITE_URI?>inventory/assets/verify?function=unverify&a=<?=$asset->getId()?>" accesskey="u">Un-Verify</a>
						<?php
						}
						
						if(!$asset->getReturnOrder() !== FALSE)
						{
							if($asset->getLocation() === NULL)
							{
							?>
							<a class="button" accesskey="a" href="<?=SITE_URI?>inventory/assets/location?function=assign&a=<?=$asset->getId()?>">Assign Location</a>
							<a class="button" accesskey="r" href="<?=SITE_URI?>inventory/assets/warehouse?function=move&a=<?=$asset->getId()?>">Change Warehouse</a>
							<?php
							}
							else
							{
							?>
							<a class="button" accesskey="w" href="<?=SITE_URI?>inventory/assets/warehouse?function=return&a=<?=$asset->getId()?>">Return To Warehouse</a>
							<a class="button" accesskey="l" href="<?=SITE_URI?>inventory/assets/location?function=move&a=<?=$asset->getId()?>">Change Location</a>
							<?php
							}
						}
						
						if(!$asset->isInWorksheet())
						{
						?>
						<a class="button" href="<?=SITE_URI?>inventory/assets/worksheet?function=add&a=<?=$asset->getId()?>" accesskey="s">Add To Worksheet (<?=$worksheet->getCount()?>)</a>
						<?php
						}
						
					if(!$asset->getReturnOrder() !== FALSE)
					{
					?>
					<a class="button confirm-button delete-button" href="<?=SITE_URI?>inventory/assets/discard?a=<?=$asset->getId()?>" accesskey="d">Discard</a>
				<?php
					}
				}
				?>
			</div>
			<h2 class="region-title">Asset Profile</h2>
			<table class="table-display asset-display">
				<tr>
					<td>Asset #</td>
					<td><?=$asset->getAssetTag()?></td>
					<td>Serial Number</td>
					<td><?=htmlentities($asset->getSerialNumber())?></td>
				</tr>
				<?php
				
				if($asset->getParent() !== NULL)
				{
					?>
					<tr>
						<td>Parent Asset #</td>
						<td><a href="view?a=<?=$parent->getId()?>"><?=$parent->getAssetTag()?></a></td>
					</tr>
					<?php
				}
				
				if($asset->getDiscarded() == 1)
				{
				?>
				<tr>
					<td>Discarded</td>
					<td>Yes</td>
					<td>Discard Date</td>
					<td><?=$asset->getDiscardDate()?></td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td>Verified</td>
					<td><?=$asset->getVerified() == 1 ? "Yes" : "No"?></td>
					<?php
					if($asset->getVerified() == 1)
					{
					?>
					<td>On</td>
					<td><?=$asset->getVerifyDate()?></td>
					<?php
					}
					?>
				</tr>
				<?php
				}
				
				if($asset->getReturnOrder() !== FALSE)
				{
					?>
					<tr>
						<td>On Return Order</td>
						<td>Yes</td>
						<td>R.O. #</td>
						<td><a href="<?=SITE_URI?>inventory/returns/view?ro=<?=$asset->getReturnOrder()?>"><?=$asset->getReturnOrder()?></a></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td>Manufacture Date</td>
					<td><?=htmlentities($asset->getManufactureDate())?></td>
					<td>In Worksheet</td>
					<td><?=$asset->isInWorksheet() ? "Yes" : "No"?></td>
				</tr>
			</table>
			<h2 class="region-title">Commodity Details</h2>
			<table class="table-display asset-display">
				<tr>
					<td>Commodity Code</td>
					<td><a href="<?=SITE_URI . "inventory/commodities/view?c=" . $commodity->getId()?>"><?=$commodity->getCode()?></a></td>
					<td>Commodity Name</td>
					<td><?=htmlentities($commodity->getName())?></td>
				</tr>
				<tr>
					<td>Manufacturer</td>
					<td><?=htmlentities($commodity->getManufacturer())?></td>
					<td>Model</td>
					<td><?=htmlentities($commodity->getModel())?></td>
				</tr>
				<tr>
					<td>Commodity Type</td>
					<td><?=htmlentities($commodityType->getName())?></td>
					<td>Asset Type</td>
					<td><?=htmlentities($assetType->getName())?></td>
				</tr>
			</table>
			<h2 class="region-title">Location Details</h2>
			<table class="table-display asset-display">
			<?php
				if($asset->getLocation() !== NULL)
				{
				?>
				<tr>
					<td>Building Code</td>
					<td><a href="<?=SITE_URI . "facilities/buildings/view?b=" . $building->getId()?>"><?=htmlentities($building->getCode())?></a></td>
					<td>Building Name</td>
					<td><?=htmlentities($building->getName())?></td>
				</tr>
				<tr>
					<td>Location Code</td>
					<td><a href="<?=SITE_URI . "facilities/locations/view?l=" . $location->getId()?>"><?=htmlentities($location->getCode())?></a></td>
					<td>Location Name</td>
					<td><?=htmlentities($location->getName())?></td>
				</tr>
				<?php
				}
				else if($asset->getWarehouse() !== NULL)
				{
				?>
				<td>Warehouse Code</td>
				<td><a href="<?=SITE_URI . "inventory/settings/warehouses/view?w=" . $warehouse->getId()?>"><?=$warehouse->getCode()?></a></td>
				<td>Warehouse Name</td>
				<td><?=$warehouse->getName()?></td>
				<?php
				}
			?>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display asset-display">
				<?php
					if($asset->getPurchaseOrder() !== NULL)
					{
					?>
					<tr>
						<td>P.O. #</td>
						<td><a href="<?=SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber()?>"><?=$purchaseOrder->getNumber()?></a></td>
						<td>Receive Date</td>
						<td><?=$purchaseOrder->getReceiveDate()?></td>
					</tr>
					<?php
					}
				?>
				<tr>
					<td>Created</td>
					<td><?=$asset->getCreateDate()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$asset->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<table class="table-display asset-display">
				<tr>
					<td>Notes</td>
					<td colspan=3><textarea readonly class="textarea-display"><?=htmlentities($asset->getNotes())?></textarea></td>
				</tr>
			</table>
			<h2 class="region-title region-expand region-expand-collapsed" id="returnHistory">Return History</h2>
			<div class="region" id="returnHistory-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<h2 class="region-title region-expand region-expand-collapsed" id="childAssets">Child Assets</h2>
			<div class="region" id="childAssets-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<script>
				<?php
					if(isset($returnHistory) AND !empty($returnHistory['data']))
					{
						?>
						showResults('returnHistory-region', <?=json_encode($returnHistory)?>, <?=RESULTS_PER_PAGE?>)
						<?php
					}
					
					if(isset($childAssets) AND !empty($childAssets['data']))
					{
						?>
						showResults('childAssets-region', <?=json_encode($childAssets)?>, <?=RESULTS_PER_PAGE?>)
						<?php
					}
				?>
			</script>
			<?php
		}
		else
			throw new AppException("Asset Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Not Defined", "P03");
?>