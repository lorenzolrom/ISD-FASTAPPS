<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
	
	if(!isset($_GET['f']))
		throw new AppException("Function Not Defined", "P03");
	else if($_GET['f'] == "add")
	{
		if(!empty($_POST))
		{
			if(!isset($_POST['name']))
				$faSystemErrors[] = "Team Name Required";
			else
			{
				$t = new sc\Team();
				if(!$t->loadFromName($_POST['name']))
					$faSystemErrors[] = "Team Not Found";
			}
			
			if(empty($faSystemErrors))
			{
				if($workspace->addTeam($t->getId()))
					exit(header("Location: " . SITE_URI . "servicenter/workspaces/view?w=" . $workspace->getId() . "&NOTICE=Team Added"));
				else
					$faSystemErrors[] = "Could Not Add Team to Workspace";
			}
		}
		
		?>
		<div class="button-bar">
			<span id="team" class="button form-submit-button" accesskey="a">Add</span>
			<a class="button" href="<?=SITE_URI?>servicenter/workspaces/view?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
		</div>
		<h2 class="region-title">Add Team</h2>
		<form class="basic-form form" method="post" id="team-form">
			<p>
				<span class="required">Team Name</span>
				<input type="text" autocomplete="off" name="name" value="<?=ifSet($_POST['name'])?>">
			</p>
		</form>
		<?php
	}
	else if($_GET['f'] == "remove")
	{
		if(!isset($_GET['t']))
			throw new AppException("Team Not Defined", "P03");
		
		if($workspace->removeTeam($_GET['t']))
			exit(header("Location: " . SITE_URI . "servicenter/workspaces/view?w=" . $workspace->getId() . "&NOTICE=Team Removed"));
		else
			$faSystemErrors[] = "Could Not Remove Team From Workspace";
	}
	else
		throw new AppException("Function Is Invalid", "P04");
?>