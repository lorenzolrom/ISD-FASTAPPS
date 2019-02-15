<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
	
	if(!empty($_POST))
	{
		/////
		// Validation
		/////
		
		$save = $workspace->changeScales($_POST);
		
		if(is_array($save))
			$faSystemErrors = $save;
		else if($save === TRUE)
			exit(header("Location: " . SITE_URI . "servicenter/workspaces/settings?w=" . $workspace->getId() . "&NOTICE=Settings Saved"));
		else
			$faSystemErrors[] = "Could Not Save Settings";
	}
	
	// Load current values
	$_POST['priority'] = $workspace->getPriorityLevels();
	$_POST['scale'] = $workspace->getScaleLevels();
?>
<div class="button-bar">
	<span id="workspace" class="button form-submit-button" accesskey="s">Save</span>
	<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
</div>
<h2 class="region-title">Scale Settings for Workspace: <?=$workspace->getName()?></h2>
<form class="basic-form form" method="post" id="workspace-form">
	<p>
		<span class="required">Priority</span>
		<input type="number" min=0 name="priority" value="<?=ifSet($_POST['priority'])?>">
	</p>
	<p>
		<span class="required">Work Scale</span>
		<input type="number" min=0 name="scale" value="<?=ifSet($_POST['scale'])?>">
	</p>
</form>