<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$vendor = new itsmcore\Vendor();
		$create = $vendor->create($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "inventory/settings/vendors/view?v=" . $vendor->getId() . "&NOTICE=Vendor Created"));
		else
			$faSystemErrors[] = "Could Not Create Vendor";
	}
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="vendor" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/vendorform.php");
?>