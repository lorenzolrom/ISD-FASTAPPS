<?php
	use itsmservices as itsmservices;
	
	if(!isset($_GET['a']))
		throw new AppException("Application Not Defined", "P03");
	
	$application = new itsmservices\Application();
	
	if(!$application->loadFromNumber($_GET['a']))
		throw new AppException("Application Is Invalid", "P04");
	
	$status = new Attribute($application->getStatus());
	$status->load();
	
	if(!empty($_POST))
	{
		$update = new itsmservices\ApplicationUpdate();
		$update->setApplication($application->getId());
		
		$create = $update->create($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "services/applications/view?a=" . $application->getNumber() . "&NOTICE=Development Status Updated"));
		else
			$faSystemErrors[] = "Could Not Update Development Status";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="application" accesskey="u">Update</span>
	<a class="button" href="<?=SITE_URI?>services/applications/view?a=<?=$application->getNumber()?>" accesskey="c">Cancel</a>
</div>
<form class="table-form form" method="post" id="application-form">
	<h2 class="region-title">Update Development Status</h2>
	<table class="table-display application-display">
		<tr>
			<td>Application #</td>
			<td><?=$application->getNumber()?></td>
			<td>Name</td>
			<td><?=htmlentities($application->getName())?></td>
		</tr>
		<tr>
			<td>Current Status</td>
			<td><?=htmlentities($status->getName())?></td>
		</tr>
		<tr>
			<td class="required">Status</td>
			<td colspan=3>
				<select name="status">
				<?php
					foreach(getAttributes('itsm', 'aits') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=$application->getStatus() == $attribute->getId() ? " selected" : ""?>><?=htmlentities($attribute->getName())?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Description</td>
			<td colspan=3><textarea name="description"><?=htmlentities(ifSet($_POST['description']))?></textarea></td>
		</tr>
	</table>
</form>