<?php
	use nisurlalias as ua;
	
	$alias = NULL;
	
	if(!isset($_GET['new'])) // Editing existing
	{
		if(!isset($_GET['a']))
			throw new AppException("Alias Not Defined", "P03");
		
		$alias = new ua\URLAlias($_GET['a']);
		if(!$alias->load())
			throw new AppException("Alias Not Found", "P04");
	}
	
	if(!empty($_POST))
	{
		if($alias === NULL AND isset($_GET['new']))
			$alias = new ua\URLAlias();
		
		if(isset($_GET['new']))
		{
			$create = $alias->create($_POST);
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Alias Created";
			else
				$faSystemErrors[] = "Could Not Create Alias";
		}
		else
		{
			$save = $alias->save($_POST);
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Changes Saved";
			else
				$faSystemErrors[] = "Could Not Save Changes";
		}
		
		if(isset($notice))
			exit(header("Location: " . SITE_URI . "webmanager/urlaliases?NOTICE=" . $notice));
	}
	
	if($alias !== NULL AND !isset($_GET['new']))
	{
		$_POST['alias'] = $alias->getAlias();
		$_POST['destination'] = $alias->getDestination();
		$_POST['disabled'] = $alias->getDisabled();
	}
?>
<div class="button-bar">
	<span id="alias" class="button form-submit-button" accesskey="s">Save</span>
	<?php
		if(!isset($_GET['new']))
		{
			?>
			<a href="<?=getURI()?>/../delete?a=<?=$alias->getId()?>" class="button confirm-button delete-button">Delete</a>
			<?php
		}
	?>
	<span class="button back-button" accesskey="c">Cancel</span>
</div>
<p class="info-message">Alias will be a subdomain of '<?=NIS_URLALIAS_DOMAIN?>'</p>
<form class="basic-form form" method="post" id="alias-form">
	<p>
		<span class="required">Alias</span>
		<input type="text" name="alias" maxlength=64 value="<?=htmlentities(ifSet($_POST['alias']))?>">
	</p>
	<p>
		<span class="required">Destination</span>
		<input type="text" name="destination" value="<?=htmlentities(ifSet($_POST['destination']))?>">
	</p>
	<p>
		<span class="required">Disabled</span>
		<select name="disabled">
			<option value="0">No</option>
			<option value="1"<?=ifSet($_POST['disabled']) == 1 ? " selected" : ""?>>Yes</option>
		</select>
	</p>
</form>