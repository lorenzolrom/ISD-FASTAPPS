<?php
	use servicenter as sc;
	
	if(!isset($_GET['t']))
		throw new AppException("Team Not Defined", "P03");
	
	$team =  new sc\Team($_GET['t']);
	if(!$team->load())
		throw new AppException("Team Is Invalid", "P04");
	
	// Build member list
	$ml['type'] = "table";
	$ml['linkColumn'] = 2;
	$ml['href'] = SITE_URI . "servicenter/teams/members?t=" . $team->getId() . "&f=remove&m=";
	$ml['head'] = ['Name', 'Username', ''];
	$ml['widths'] = ["", "", "10px"];
	$ml['align'] = ["left", "left", "center"];
	$ml['data'] = [];
	$ml['refs'] = [];
	
	foreach($team->getMembers() as $m)
	{
		$ml['refs'][] = [$m->getId()];
		$ml['data'][] = [$m->getFirstName() . " " . $m->getLastName(), $m->getUsername(), "REMOVE"];
	}
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../edit?t=<?=$team->getId()?>" accesskey="e">Edit</a>
	<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?t=<?=$team->getId()?>" accesskey="d">Delete</a>
	<a class="button" href="<?=getURI()?>/../new?new" accesskey="c">Create</a>
</div>
<h2 class="region-title">Team Profile</h2>
<table class="table-display team-display">
	<tr>
		<td>Team Name</td>
		<td><?=$team->getName()?></td>
	</tr>
</table>
<h2 class="region-title region-expand region-expand-collapsed" id="members">Team Members
	<a href="<?=getURI()?>/../members?t=<?=$team->getId()?>&f=add" class="button-noveil" accesskey="a">Add Member</a>
</h2>
<div class="region" id="members-region">
	<span class="red-message">NO DATA FOUND</span>
</div>
<?php
	if(isset($ml) AND !empty($ml['data']))
	{
		?>
		<script>showResults('members-region', <?=json_encode($ml)?>, <?=RESULTS_PER_PAGE?>)</script>
		<?php
	}
?>