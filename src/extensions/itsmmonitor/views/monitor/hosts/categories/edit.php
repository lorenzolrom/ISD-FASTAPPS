<?php
	use itsmmonitor as itsmmonitor;
	use itsmcore as itsmcore;
	
	$category = NULL;
	
	if(!isset($_GET['new'])) // Editing existing
	{
		if(!isset($_GET['c']))
			throw new AppException("Category Not Defined", "P03");
		
		$category = new itsmmonitor\HostCategory($_GET['c']);
		if(!$category->load())
			throw new AppException("Category Not Found", "P04");
	}
	
	if(!empty($_POST))
	{
		if($category === NULL AND isset($_GET['new']))
			$category = new itsmmonitor\HostCategory();
		
		if(isset($_GET['new']))
		{
			$create = $category->create($_POST);
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Category Created";
			else
				$faSystemErrors[] = "Could Not Create Category";
		}
		else
		{
			$save = $category->save($_POST);
			
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Changes Saved";
			else
				$faSystemErrors[] = "Could Not Save Changes";
		}
		
		if(isset($notice))
			exit(header("Location: " . SITE_URI . "monitor/hosts/categories?NOTICE=" . $notice));
	}
	
	if($category !== NULL AND !isset($_GET['new']))
	{
		// Load details
		$_POST['name'] = $category->getName();
		$_POST['displayed'] = $category->getDisplayed();
		$_POST['hosts'] = [];
		
		foreach($category->getHosts() as $host)
		{
			$_POST['hosts'][] = $host->getId();
		}
	}
	
	if(!isset($_POST['hosts']) OR !is_array($_POST['hosts']))
		$_POST['hosts'] = [];
?>
<div class="button-bar">
	<span id="category" class="button form-submit-button" accesskey="s">Save</span>
	<?php
		if($category !== NULL AND !isset($_GET['new']))
		{
			?>
			<a class="button confirm-button delete-button" href="<?=SITE_URI?>monitor/hosts/categories/delete?c=<?=$category->getId()?>" accesskey="d">Delete</a>
			<?php
		}
	?>
	<span class="button back-button" accesskey="c">Cancel</span>
</div>
<form class="basic-form form" method="post" id="category-form">
	<p>
		<span class="required">Name</span>
		<input type="text" autocomplete="off" name="name" maxlength=64 value="<?=htmlentities(ifSet($_POST['name']))?>">
	</p>
	<p>
		<span class="required">Displayed</span>
		<select name="displayed">
			<option value="0">No</option>
			<option value="1"<?=ifSet($_POST['displayed']) == 1 ? " selected" : ""?>>Yes</option>
		</select>
	</p>
	<p>
		<span>Hosts</span>
		<select name="hosts[]" multiple size=10>
		<?php
			foreach(itsmcore\getHosts() as $host)
			{
			?>
			<option value="<?=$host->getId()?>"<?=in_array($host->getId(), $_POST['hosts']) ? " selected" : ""?>><?=$host->getSystemName() . " (" . $host->getIpAddress() . ")"?></option>
			<?php
			}
		?>
		</select>
	</p>
</form>