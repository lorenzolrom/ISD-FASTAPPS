<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['b']))
	{
		$building = new facilitiescore\Building($_GET['b']);
		
		if($building->load())
		{
			$lastModifyUser = new \User($building->getLastModifyUser());
			$lastModifyUser->load();
			
			$createUser = new \User($building->getCreateUser());
			$createUser->load();
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?b=<?=$building->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?b=<?=$building->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Building Profile</h2>
			<table class="table-display building-display">
				<tr>
					<td>Building Code</td>
					<td><?=htmlentities($building->getCode())?></td>
					<td>Building Name</td>
					<td><?=htmlentities($building->getName())?></td>
				</tr>
				<tr>
					<td>Street Address</td>
					<td><?=htmlentities($building->getStreetAddress())?></td>
					<td>City</td>
					<td><?=htmlentities($building->getCity())?></td>
				</tr>
				<tr>
					<td>State</td>
					<td><?=htmlentities($building->getState())?></td>
					<td>Zip Code</td>
					<td><?=htmlentities($building->getZipCode())?></td>
				</tr>
			</table>
			
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$building->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$building->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Building Is Invalid", "P04");
	}
	else
		throw new AppException("Building Not Defined", "P03");
?>