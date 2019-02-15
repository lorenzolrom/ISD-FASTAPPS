<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
?>
<h2 class="region-title">Settings for Workspace: <?=$workspace->getName()?></h2>
<ul class="list-menu">
	<li><a href="<?=SITE_URI?>servicenter/workspaces/settings/attributes?w=<?=$workspace->getId()?>">Ticket Attributes</a></li>
	<li><a href="<?=SITE_URI?>servicenter/workspaces/settings/scales?w=<?=$workspace->getId()?>">Ticket Scales</a></li>
	<li><a href="<?=SITE_URI?>servicenter/workspaces/settings/widgets?w=<?=$workspace->getId()?>">Service Desk Widgets</a></li>
</ul>