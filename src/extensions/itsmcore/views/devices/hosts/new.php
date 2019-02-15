<?php
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		if(empty($faSystemErrors))
		{
			$host = new itsmcore\Host();
			$create = $host->create($_POST);
			
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				exit(header("Location: " . SITE_URI . "devices/hosts/view?h=" . $host->getId() . "&NOTICE=Host Created"));
			else
				$faSystemErrors[] = "Could Not Create Host";
		}
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="host" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/hostform.php");
?>