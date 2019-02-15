<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		$create = $purchaseOrder->create($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Purchase Order Created"));
		else
			$faSystemErrors[] = "Could Not Create Purchase Order";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="purchaseorder" accesskey="s">Save</span>
	<span class="button back-button" accesskey="c">Cancel</span>
</div>
<?php
	require_once(dirname(__FILE__) . "/purchaseorderform.php");
?>