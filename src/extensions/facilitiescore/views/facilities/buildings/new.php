<?php
	use facilitiescore as facilitiescore;
	
	if(!empty($_POST))
	{
		$building =  new facilitiescore\Building();
		$create = $building->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "facilities/buildings/view?b=" . $building->getId() . "&NOTICE=Building Created"));
		else
			$faSystemErrors[] = "Could Not Create Building";
	}
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="building" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__). "/buildingform.php");
?>