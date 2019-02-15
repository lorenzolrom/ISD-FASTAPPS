<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
	
	// Build member list
	$tl['type'] = "table";
	$tl['linkColumn'] = 1;
	$tl['href'] = SITE_URI . "servicenter/workspaces/teams?w=" . $workspace->getId() . "&f=remove&t=";
	$tl['head'] = ['Name', ''];
	$tl['widths'] = ["", "10px"];
	$tl['align'] = ["left", "center"];
	$tl['data'] = [];
	$tl['refs'] = [];
	
	foreach($workspace->getTeams() as $t)
	{
		$tl['refs'][] = [$t->getId()];
		$tl['data'][] = [$t->getName(), "REMOVE"];
	}
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../edit?w=<?=$workspace->getId()?>" accesskey="e">Edit</a>
	<a class="button" href="<?=getURI()?>/../settings?w=<?=$workspace->getId()?>" accesskey="s">Workspace Settings</a>
	<a class="button" href="<?=getURI()?>/../new?new" accesskey="c">Create</a>
</div>
<h2 class="region-title">Workspace Profile</h2>
<table class="table-display workspace-display">
	<tr>
		<td>Workspace Name</td>
		<td><?=$workspace->getName()?></td>
		<td>Default Workspace?</td>
		<td><?=$workspace->getDefault() == 1 ? "Yes" : "No"?></td>
	</tr>
</table>
<h2 class="region-title region-expand region-expand-collapsed" id="teams">Workspace Teams
	<a href="<?=getURI()?>/../teams?w=<?=$workspace->getId()?>&f=add" class="button-noveil" accesskey="a">Add Team</a>
</h2>
<div class="region" id="teams-region">
	<span class="red-message">NO DATA FOUND</span>
</div>
<?php
	if(isset($tl) AND !empty($tl['data']))
	{
		?>
		<script>showResults('teams-region', <?=json_encode($tl)?>, <?=RESULTS_PER_PAGE?>)</script>
		<?php
	}
?>