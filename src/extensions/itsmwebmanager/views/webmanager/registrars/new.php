<?php
	use itsmwebmanager as itsmwebmanager;
	
	if(!empty($_POST))
	{
		$registrar = new itsmwebmanager\Registrar();
		$create = $registrar->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "webmanager/registrars/view?r=" . $registrar->getId() . "&NOTICE=Registrar Created"));
		else
			$faSystemErrors[] = "Could Not Create Registrar";
	}
	
	?>
	<div class="button-bar">
		<span class="button form-submit-button" id="registrar" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/registrarform.php");
?>