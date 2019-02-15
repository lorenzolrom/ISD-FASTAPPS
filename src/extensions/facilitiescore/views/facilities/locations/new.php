<?php
	use facilitiescore as facilitiescore;
	
	if(!empty($_POST))
	{
		$location = new facilitiescore\Location();
		$create = $location->create($_POST);
			
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "facilities/locations/view?l=" . $location->getId() . "&NOTICE=Location Created"));
		else
			$faSystemErrors[] = "Could Not Create Location";
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="location" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/locationform.php");
?>