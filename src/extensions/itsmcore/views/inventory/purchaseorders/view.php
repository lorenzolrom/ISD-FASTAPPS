<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['po']))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		if($purchaseOrder->loadFromNumber($_GET['po']))
		{
			$vendor = new itsmcore\Vendor($purchaseOrder->getVendor());
			$warehouse = new itsmcore\Warehouse($purchaseOrder->getWarehouse());
			$lastModifyUser = new User($purchaseOrder->getLastModifyUser());
			$createUser = new User($purchaseOrder->getCreateUser());
			$status = new Attribute($purchaseOrder->getStatus());
			
			$vendor->load();
			$warehouse->load();
			$lastModifyUser->load();
			$createUser->load();
			$status->load();
			
			// List commodities
			$commodities = $purchaseOrder->getCommodities();
			
			$commodityResults['type'] = "table";
			$commodityResults['linkColumn'] = 5;
			$commodityResults['href'] = SITE_URI . "inventory/purchaseorders/commodities?POPUP=yes&function=remove&po=" . $purchaseOrder->getNumber() . "&id=";
			$commodityResults['head'] = ['Code', 'Name', 'Quantity', 'Unit Cost', 'Total Cost', ''];
			$commodityResults['refs'] = [];
			$commodityResults['data'] = [];
			$commodityResults['classes'] = [];
			$commodityResults['widths'] = ["10px", "", "10px", "100px", "100px", "10px"];
			$commodityResults['align'] = ["right", "left", "left", "right", "right", "left"];
			
			foreach($commodities as $commodityRow)
			{
				$commodity = new itsmcore\Commodity($commodityRow['commodity']);
				$commodity->load();

				$commodityResults['refs'][] = $commodityRow['id'];
				$commodityResults['classes'] = ["", "", "", "", "", "itsmcore-small-popup-link-td"];
				$commodityResults['data'][] = [$commodity->getCode(), $commodity->getName(), $commodityRow['quantity'], $commodityRow['unitCost'], ($commodityRow['quantity'] * $commodityRow['unitCost']), 'REMOVE'];
			}
			
			// List cost items
			$costItems = $purchaseOrder->getCostItems();
			
			$costItemResults['type'] = "table";
			$costItemResults['linkColumn'] = 2;
			$costItemResults['href'] = SITE_URI . "inventory/purchaseorders/costitems?POPUP=yes&function=remove&po=" . $purchaseOrder->getNumber() . "&id=";
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
				if($purchaseOrder->getSent() == 0) // Order not sent
				{
					?><a class="button" href="<?=getURI()?>/../edit?po=<?=$purchaseOrder->getNumber()?>" accesskey="e">Edit</a>
					<?php
						if(sizeof($purchaseOrder->getCommodities()) > 0) // Order has commodities
						{
						?><a class="button confirm-button" href="<?=getURI()?>/../process?function=send&po=<?=$purchaseOrder->getNumber()?>" accesskey="s">Send</a>
						<?php
						}
					?><a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?po=<?=$purchaseOrder->getNumber()?>" accesskey="d">Delete</a>
					<?php
				}
				else if($purchaseOrder->getSent() == 1 AND $purchaseOrder->getReceived() == 0 AND $purchaseOrder->getCanceled() == 0) // Order sent but not received or canceled
				{
					?><a class="button" href="<?=getURI()?>/../process?function=receive&po=<?=$purchaseOrder->getNumber()?>" accesskey="r">Receive</a>
					<a class="button confirm-button" href="<?=getURI()?>/../process?function=cancel&po=<?=$purchaseOrder->getNumber()?>" accesskey="a">Cancel</a>
					<?php
				}
				?><a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Purchase Order Profile</h2>
			<table class="table-display purchaseorder-display">
				<tr>
					<td>P.O. #</td>
					<td><?=$purchaseOrder->getNumber()?></td>
					<td>Status</td>
					<td><?=htmlentities($status->getName());?></td>
				</tr>
			</table>
			<h2 class="region-title">Order Details</h2>
			<table class="table-display purchaseorder-display">
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
					<td><?=$purchaseOrder->getOrderDate()?></td>
				</tr>
				<tr>
					<td>Sent</td>
					<td><?=($purchaseOrder->getSent() == 1) ? "Yes" : "No"?></td>
					<td>Send Date</td>
					<td><?=$purchaseOrder->getSendDate()?></td>
				</tr>
				<tr>
					<td>Received</td>
					<td><?=($purchaseOrder->getReceived() == 1) ? "Yes" : "No"?></td>
					<td>Receive Date</td>
					<td><?=$purchaseOrder->getReceiveDate()?></td>
				</tr>
				<tr>
					<td>Canceled</td>
					<td><?=($purchaseOrder->getCanceled() == 1) ? "Yes" : "No"?></td>
					<td>Cancel Date</td>
					<td><?=$purchaseOrder->getCancelDate()?></td>
				</tr>
			</table>
			<h2 class="region-title">Commodities<?php
			if($purchaseOrder->getSent() == 0)
			{
				?>
				<a href="<?=getURI()?>/../commodities?POPUP=yes&function=add&po=<?=$purchaseOrder->getNumber()?>" class="button-noveil itsmcore-small-popup-link" accesskey="o">Add Commodity</a>
				<?php
			}
			?></h2>
			<div id="commodityResults">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<h2 class="region-title">Cost Items<?php
			if($purchaseOrder->getSent() == 0)
			{
				?>
				<a href="<?=getURI()?>/../costitems?POPUP=yes&function=add&po=<?=$purchaseOrder->getNumber()?>" class="button-noveil itsmcore-small-popup-link" accesskey="i">Add Cost Item</a>
				<?php
			}
			?></h2>
			<div id="costItemResults">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display purchaseorder-display">
				<tr>
					<td>Notes</td>
					<td colspan=3><textarea readonly class="textarea-display"><?=htmlentities($purchaseOrder->getNotes())?></textarea></td>
				</tr>
			</table>
			<table class="table-display purchaseorder-display">
				<tr>
					<td>Created</td>
					<td><?=$purchaseOrder->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$purchaseOrder->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
			if(isset($commodityResults) AND !empty($commodityResults['data']))
			{
				?>
				<script>showResults('commodityResults', <?=json_encode($commodityResults)?>, 5)</script>
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