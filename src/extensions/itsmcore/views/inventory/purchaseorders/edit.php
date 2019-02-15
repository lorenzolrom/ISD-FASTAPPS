<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['po']))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		if($purchaseOrder->loadFromNumber($_GET['po']))
		{
			// Check for sent, received, canceled
			if($purchaseOrder->getReceived() == 1)
				throw new AppException("Purchase Order Has Been Received", "P04");
			
			if($purchaseOrder->getCanceled() == 1)
				throw new AppException("Purchase Order Has Been Canceled", "P04");
			
			if($purchaseOrder->getSent() == 1)
				throw new AppException("Purchase Order Has Been Sent", "P04");
			
			$vendor = new itsmcore\Vendor($purchaseOrder->getVendor());	
			$warehouse = new itsmcore\Warehouse($purchaseOrder->getWarehouse());
			$vendor->load();
			$warehouse->load();
			
			if(!empty($_POST))
			{
				$save = $purchaseOrder->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['vendorCode'] = $vendor->getCode();
			$_POST['vendorName'] = $vendor->getName();
			$_POST['warehouseCode'] = $warehouse->getCode();
			$_POST['warehouseName'] = $warehouse->getName();
			
			$_POST['orderDate'] = $purchaseOrder->getOrderDate();
			$_POST['notes'] = $purchaseOrder->getNotes();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="purchaseorder" accesskey="s">Save</span>
				<a class="button back-button" href="<?=SITE_URI?>inventory/purchaseorders/view?po=<?=$purchaseOrder->getNumber()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/purchaseorderform.php");
		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
?>