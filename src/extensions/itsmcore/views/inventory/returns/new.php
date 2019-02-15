<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		$create = $returnOrder->create($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "inventory/returns/view?ro=" . $returnOrder->getNumber() . "&NOTICE=Return Order Created"));
		else
			$faSystemErrors[] = "Could Not Create Return Order";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="returnorder" accesskey="s">Save</span>
	<span class="button back-button" accesskey="c">Cancel</span>
</div>
<?php
	require_once(dirname(__FILE__) . "/returnorderform.php");
?>