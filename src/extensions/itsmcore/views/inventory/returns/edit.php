<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			if(!empty($_POST))
			{
				$save = $returnOrder->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/returns/view?ro=" . $returnOrder->getNumber() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$vendor = new itsmcore\Vendor($returnOrder->getVendor());
			$vendor->load();
			$warehouse = new itsmcore\Warehouse($returnOrder->getWarehouse());
			$warehouse->load();
			
			$_POST['returnType'] = $returnOrder->getReturnType();
			$_POST['vendorRMA'] = $returnOrder->getVendorRMA();
			$_POST['orderDate'] = $returnOrder->getOrderDate();
			$_POST['notes'] = $returnOrder->getNotes();
			$_POST['vendorCode'] = $vendor->getCode();
			$_POST['vendorName'] = $vendor->getName();
			$_POST['warehouseCode'] = $warehouse->getCode();
			$_POST['warehouseName'] = $warehouse->getName();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="returnorder" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>inventory/returns/view?ro=<?=$returnOrder->getNumber()?>" accesskey="c">Cancel</a>
			</div>
			<?php
				require_once(dirname(__FILE__) . "/returnorderform.php");
		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
?>