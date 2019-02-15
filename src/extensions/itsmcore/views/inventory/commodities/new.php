<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$commodity = new itsmcore\Commodity;
		$create = $commodity->create($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "inventory/commodities/view?c=" . $commodity->getId() . "&NOTICE=Commodity Created"));
		else
			$faSystemErrors[] = "Could Not Create Commodity";
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="commodity" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/commodityform.php");
?>