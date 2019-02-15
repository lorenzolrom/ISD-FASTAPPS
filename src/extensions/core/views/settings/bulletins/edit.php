<?php
	$bulletin = null;

	// Editing existing entry
	if(!isset($_GET['new']))
	{
		if(!isset($_GET['b']))
			throw new AppException("Bulletin Not Defined", "P03");
		
		$bulletin = new Bulletin($_GET['b']);
		if(!$bulletin->load())
			throw new AppException("Bulletin Not Found", "P04");
	}
	
	// Process form submission
	if(!empty($_POST))
	{
		if(isset($_GET['new']))
		{
			$bulletin = new Bulletin();
			
			$create = $bulletin->create($_POST);
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Bulletin Created";
			else
				$faSystemErrors[] = "Could Not Create Bulletin";
		}
		else
		{
			$save = $bulletin->save($_POST);
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Changes Saved";
			else
				$faSystemErrors[] = "Could Not Save Changes";
		}
		
		if(isset($notice))
			exit(header("Location: " . getURI() . "/../?NOTICE=" . $notice));
	}
	
	// Load existing information into form
	if($bulletin !== NULL AND !isset($_GET['new']))
	{
		$_POST['title'] = $bulletin->getTitle();
		$_POST['message'] = $bulletin->getMessage();
		$_POST['inactive'] = $bulletin->getInactive();
		$_POST['type'] = $bulletin->getBulletinType();
		$_POST['startDate'] = $bulletin->getStartDate();
		$_POST['endDate'] = $bulletin->getEndDate();
		
		$_POST['roles'] = $bulletin->getRoles();
	}
?>
<form class="basic-form form" method="post">
	<p>
		<span class="required">Title</span>
		<input type="text" name="title" value="<?=ifSet($_POST['title'])?>" maxlength="64">
	</p>
	<p>
		<span class="required">Type</span>
		<select name="type">
			<option value="i">Info</option>
			<option value="a"<?=ifSet($_POST['type']) == "a" ? " selected" : ""?>>Alert</option>
		</select>
	</p>
	<p>
		<span class="required">Message</span>
		<textarea name="message"><?=ifSet($_POST['message'])?></textarea>
	</p>
	<p>
		<span class="required">Inactive</span>
		<select name="inactive">
			<option value="0">No</option>
			<option value="1"<?=ifSet($_POST['inactive']) == "1" ? " selected" : ""?>>Yes</option>
		</select>
	</p>
	<p>
		<span class="required">Start Date</span>
		<input class="date-input" type="text" name="startDate" value="<?=ifSet($_POST['startDate'])?>">
	</p>
	<p>
		<span>End Date</span>
		<input class="date-input" type="text" name="endDate" value="<?=ifSet($_POST['endDate'])?>">
	</p>
	<p>
		<span>Roles</span>
		<select name="roles[]" multiple size="5">
			<?php
				$roles = getRoles();
				foreach($roles as $role)
				{
					?>
					<option value="<?=$role->getId()?>"<?=(isset($_POST['roles']) AND in_array($role->getId(), $_POST['roles'])) ? " selected" : ""?>><?=$role->getName()?></option>
					<?php
				}
			?>
		</select>
	</p>
	<input type="submit" class="button" value="Save" accesskey="s">
	<input type="button" class="button back-button" value="Cancel" accesskey="c">
</form>