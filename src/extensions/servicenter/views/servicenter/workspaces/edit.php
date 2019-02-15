<?php
	use servicenter as sc;
	
	$workspace = null;
	
	if(!isset($_GET['new'])) // Editing existing
	{
		if(!isset($_GET['w']))
			throw new AppException("Workspace Not Defined", "P03");
		
		$workspace =  new sc\Workspace($_GET['w']);
		if(!$workspace->load())
			throw new AppException("Workspace Is Invalid", "P04");
	}
	
	if(!empty($_POST))
	{
		if($workspace === NULL AND isset($_GET['new']))
			$workspace = new sc\Workspace();		
		
		if(isset($_GET['new']))
		{
			$create = $workspace->create($_POST);
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Workspace Created";
			else
				$faSystemErrors[] = "Could Not Create Workspace";
		}
		else
		{
			$save = $workspace->save($_POST);
			
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Changes Saved";
			else
				$faSystemErrors[] = "Could Not Save Changes";
		}
		
		if(isset($notice))
			exit(header("Location: " . SITE_URI . "servicenter/workspaces/view?w=" . $workspace->getId() . "&NOTICE=" . $notice));
	}
	
	if($workspace !== NULL AND !isset($_GET['new']))
	{
		// Load Details
		$_POST['name'] = $workspace->getName();
		$_POST['default'] = $workspace->getDefault();
	}
?>
<div class="button-bar">
		<span id="workspace" class="button form-submit-button" accesskey="s">Save</span>
		<?php
			if(!isset($_GET['new']))
			{
				?>
				<a class="button" href="<?=SITE_URI?>servicenter/workspaces/view?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
				<?php
			}
			else
			{
				?>
				<span class="button back-button" accesskey="c">Cancel</span>
				<?php
			}
		?>
	</div>
<form class="basic-form form" method="post" id="workspace-form">
	<p>
		<span class="required">Name</span>
		<input type="text" autocomplete="off" name="name" maxlength=64 value="<?=ifSet($_POST['name'])?>">
	</p>
	<p>
		<span class="required">Default Workspace</span>
		<select name="default">
			<option value="0">No</option>
			<option value="1"<?=ifSet($_POST['default']) == 1 ? " selected" : ""?>>Yes</option>
		</select>
	</p>
</form>