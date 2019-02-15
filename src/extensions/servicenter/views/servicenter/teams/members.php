<?php
	use servicenter as sc;
	
	if(!isset($_GET['t']))
		throw new AppException("Team Not Defined", "P03");
	
	$team =  new sc\Team($_GET['t']);
	if(!$team->load())
		throw new AppException("Team Is Invalid", "P04");
	
	if(!isset($_GET['f']))
		throw new AppException("Function Not Defined", "P03");
	else if($_GET['f'] == "add")
	{
		if(!empty($_POST))
		{
			$add = $team->addMember($_POST);
			
			if(is_array($add))
				$faSystemErrors = $add;
			else if($add === FALSE)
				$faSystemErrors[] = "Could Not Add User To Team";
			else
				exit(header("Location: " . SITE_URI . "servicenter/teams/view?t=" . $team->getId() . "&NOTICE=User Added"));
		}
		
		?>
		<div class="button-bar">
			<span id="team" class="button form-submit-button" accesskey="a">Add</span>
			<a class="button" href="<?=SITE_URI?>servicenter/teams/view?t=<?=$team->getId()?>" accesskey="c">Cancel</a>
		</div>
		<h2 class="region-title">Add Member</h2>
		<form class="basic-form form" method="post" id="team-form">
			<p>
				<span class="required">Username</span>
				<input type="text" autocomplete="off" name="username" value="<?=ifSet($_POST['username'])?>">
			</p>
		</form>
		<?php
	}
	else if($_GET['f'] == "remove")
	{
		if(!isset($_GET['m']))
			throw new AppException("User Not Defined", "P03");
		
		if($team->removeMember($_GET['m']))
			exit(header("Location: " . SITE_URI . "servicenter/teams/view?t=" . $team->getId() . "&NOTICE=User Removed"));
		else
			$faSystemErrors[] = "Could Not Remove User From Team";
	}
	else
		throw new AppException("Function Is Invalid", "P04");
?>