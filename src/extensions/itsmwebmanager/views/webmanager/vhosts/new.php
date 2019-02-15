<?php
	use itsmwebmanager as itsmwebmanager;
	use itsmcore as itsmcore;
	
	if(!empty($_POST))
	{
		$vhost = new itsmwebmanager\VHost();
		$create = $vhost->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "webmanager/vhosts/view?v=" . $vhost->getId() . "&NOTICE=VHost Created"));
		else
			$faSystemErrors[] = "Could Not Create VHost";
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="vhost" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/vhostform.php");
?>