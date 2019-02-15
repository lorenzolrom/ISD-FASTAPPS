<?php
	use servicenter as sc;
	
	$team = null;
	
	if(!isset($_GET['new'])) // Editing existing
	{
		if(!isset($_GET['t']))
			throw new AppException("Team Not Defined", "P03");
		
		$team =  new sc\Team($_GET['t']);
		if(!$team->load())
			throw new AppException("Team Is Invalid", "P04");
	}
	
	if(!empty($_POST))
	{
		if($team === NULL AND isset($_GET['new']))
			$team = new sc\Team();
		
		if(isset($_GET['new']))
		{
			$create = $team->create($_POST);
			
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === FALSE)
				$faSystemErrors[] = "Could Not Create Team";
			else
				$notice = "Team Created";
		}
		else
		{
			$save = $team->save($_POST);
			
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === FALSE)
				$faSystemErrors[] = "Could Not Save Changes";
			else
				$notice = "Changes Saved";
		}
		
		if(isset($notice))
			exit(header("Location: " . SITE_URI . "servicenter/teams/view?t=" . $team->getId() . "&NOTICE=" . $notice));
	}
	
	if($team !== NULL AND !isset($_GET['new']))
	{
		// Load Details
		$_POST['name'] = $team->getName();
	}
?>
<div class="button-bar">
		<span id="team" class="button form-submit-button" accesskey="s">Save</span>
		<?php
			if(!isset($_GET['new']))
			{
				?>
				<a class="button" href="<?=SITE_URI?>servicenter/teams/view?t=<?=$team->getId()?>" accesskey="c">Cancel</a>
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
<form class="basic-form form" method="post" id="team-form">
	<p>
		<span class="required">Name</span>
		<input type="text" autocomplete="off" name="name" maxlength=64 value="<?=ifSet($_POST['name'])?>">
	</p>
</form>