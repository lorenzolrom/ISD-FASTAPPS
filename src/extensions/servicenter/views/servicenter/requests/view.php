<?php use servicenter as sc;

	// Load Workspace
	if(!isset($_GET['w']))
		throw new AppException('Workspace Not Defined', 'P03');
	$workspace = new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException('Workspace Not Found', 'P04');
	
	// Load Ticket
	if(!isset($_GET['t']))
		throw new AppException('Ticket Not Defined', 'P03');
	$ticket = new sc\Ticket();
	if(!$ticket->loadFromNumber($workspace->getId(), $_GET['t']))
		throw new AppException('Ticket Not Found', 'P04');
	
	// Check User
	if($ticket->getContact() !== $faCurrentUser->getId())
		throw new AppException('Ticket Not Found', 'S01');
	
	$category = new sc\WorkspaceAttribute($ticket->getCategory());
	$category->load();
	$type = new sc\WorkspaceAttribute($ticket->getTicketType());
	$type->load();
	$severity = new sc\WorkspaceAttribute($ticket->getSeverity());
	$severity->load();
	
	$statusCode = $ticket->getStatus();
	$status = new Attribute();
	if(!$status->loadFromCode('srvc', 'tsta', $statusCode))
	{
		$status = new sc\WorkspaceAttribute();
		$status->loadFromCode($workspace->getId(), 'tsta', $statusCode);
	}
	
	if($statusCode == "clos")
	{
		$closureCodeCode = $ticket->getClosureCode();
		$closureCode = new Attribute();
		if(!$closureCode->loadFromCode('srvc', 'tclc', $closureCodeCode))
		{
			$closureCode = new sc\WorkspaceAttribute();
			$closureCode->loadFromCode($workspace->getId(), 'tclc', $closureCodeCode);
		}
	}
	
	// Get Ticket Updates
	$ticketUpdates = $ticket->getDetails('u');
	
	$updates['type'] = "description";
	$updates['data'] = [];
	
	foreach($ticketUpdates as $update)
	{
		$updateUser = new User($update->getUser());
		$updateUser->load();
		
		$description = [];
		$description['header'] = $updateUser->getFirstName() . " " . $updateUser->getLastName() . " on " . $update->getDetailDate();
		$description['description'] = $update->getData();
		$updates['data'][] = $description;
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<a href="<?=SITE_URI . "servicenter/requests/new"?>" class="button" accesskey="n">New Request</a>
	<?php
		if($ticket->getStatus() != "clos")
		{
		?>
			<a href="<?=SITE_URI . "servicenter/requests/update?w=" . $workspace->getId() . "&t=" . $ticket->getNumber()?>" class="button" accesskey="u">Update</a>
		<?php
		}
	?>
</div>
<h2 class="region-title"><?=$workspace->getName()?> Ticket #<?=$ticket->getNumber()?></h2>
<table class="table-display ticket-display">
	<tr>
		<td>Title</td>
		<td><?=htmlentities($ticket->getTitle())?></td>
		<td>Created</td>
		<td><?=$ticket->getCreateDate()?></td>
	</tr>
	<tr>
		<td>Type</td>
		<td><?=$type->getName()?></td>
		<td>Category</td>
		<td><?=$category->getName()?></td>
	</tr>
	<tr>
		<td>Severity</td>
		<td><?=$severity->getName()?></td>
		<td>Desired Due Date</td>
		<td><?=$ticket->getDesiredDueDate()?></td>
	</tr>
	<tr>
		<td>Status</td>
		<td><?=$status->getName()?></td>
		<?php
			if($statusCode == 'clos')
			{
			?>
			<td>Closure Code</td>
			<td></td>
			<?php
			}
		?>
	</tr>
	<tr>
		<td>Location</td>
		<td><?=htmlentities($ticket->getLocation())?></td>
	</tr>
</table>
<h2 class="region-title">Updates</h2>
<div id="updates">
	<span class="red-message">NO DATA FOUND</span>
</div>
<?php
	if(isset($updates) AND !empty($updates['data']))
	{
		?>
		<script>
			showResults('updates', <?=json_encode($updates)?>, 1);
		</script>
		<?php
	}
?>