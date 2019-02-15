<?php
	use facilitiesfloorplans as fp;
	use facilitiescore as fc;
	
	if(!isset($_GET['f']))
		throw new AppException("Floorplan Not Defined", "P03");
	
	$floorplan = new fp\Floorplan($_GET['f']);
	if(!$floorplan->load())
		throw new AppException("Floorplan Not Found", "P04");
	
	$building = new fc\Building($floorplan->getBuilding());
	$building->load();
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../edit?f=<?=$floorplan->getId()?>" accesskey="e">Edit</a>
	<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?f=<?=$floorplan->getId()?>" accesskey="d">Delete</a>
	<a class="button" href="<?=getURI()?>/../new?new" accesskey="c">Create</a>
</div>
<h2 class="region-title">Floorplan Profile</h2>
<table class="table-display">
	<tr>
		<td>Building Code</td>
		<td><a href="<?=getURI()?>/../../buildings/view?b=<?=$building->getId()?>"><?=htmlentities($building->getCode())?></a></td>
		<td>Building Name</td>
		<td><?=htmlentities($building->getName())?></td>
	</tr>
	<tr>
		<td>Floor Name</td>
		<td><?=htmlentities($floorplan->getFloor())?></td>
	</tr>
</table>
<div class="floorplan-image">
	<img src="<?= FACILITIES_FLOORPLANS_IMAGEURI . $floorplan->getImagePath()?>" alt="">
</div>