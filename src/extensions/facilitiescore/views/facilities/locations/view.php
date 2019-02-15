<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['l']))
	{
		$location = new facilitiescore\Location($_GET['l']);
		
		if($location->load())
		{
			$lastModifyUser = new \User($location->getLastModifyUser());
			$lastModifyUser->load();
			
			$createUser = new \User($location->getCreateUser());
			$createUser->load();
			
			$building = new facilitiescore\Building($location->getBuilding());
			$building->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?l=<?=$location->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?l=<?=$location->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Location Profile</h2>
			<table class="table-display building-display">
				<tr>
					<td>Building Code</td>
					<td><a href="<?=SITE_URI . "facilities/buildings/view?b=" . $building->getId()?>"><?=htmlentities($building->getCode())?></a></td>
					<td>Building Name</td>
					<td><?=htmlentities($building->getName())?></td>
				</tr>
				<tr>
					<td>Location Code</td>
					<td><?=htmlentities($location->getCode())?></td>
					<td>Location Name</td>
					<td><?=htmlentities($location->getName())?></td>
				</tr>
			</table>
			
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$location->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$location->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Location Is Invalid", "P04");
	}
	else
		throw new AppException("Location Not Defined", "P03");
?>