<?php
	use servicenter as sc;
	
	$workspace = sc\getDefaultWorkspace();
	
	if($workspace === FALSE)
		throw new AppException("No Workspace Has Been Designated For Requests", "B02");
	
	$tickets = [];
	
	if(ifSet($_GET['r']) == "closed")
		$tickets = sc\getTickets([], "%", "%", $faCurrentUser->getUsername(), [], [], [], [], [], [], ['clos']);
	else if(ifSet($_GET['r']) == "all")
		$tickets = sc\getTickets([], "%", "%", $faCurrentUser->getUsername());
	else
		$tickets = sc\getTickets([], "%", "%", $faCurrentUser->getUsername(), [], [], [], [], [], [], [], [], ['clos']);
	
	// Build results array
	
	
	$results['type'] = "table";
	$results['linkColumn'] = 1;
	$results['href'] = getURI() . "/view?w=";
	$results['head'] = ['Workspace', 'Number', 'Title', 'Type', 'Category', 'Status', 'Description', 'Updated'];
	$results['widths'] = ["100px", "10px", "200px", "100px", "100px", "50px", "", "150px"];
	$results['align'] = ["right", "center", "left", "left", "left", "left", "left", "right"];
	$results['refs'] = [];
	$results['data'] = [];
	
	foreach($tickets as $ticket)
	{
		$type = new sc\WorkspaceAttribute($ticket->getTicketType());
		$type->load();
		$category = new sc\WorkspaceAttribute($ticket->getCategory());
		$category->load();
		$ws = new sc\Workspace($ticket->getWorkspace());
		$ws->load();
		$statusCode = $ticket->getStatus();
		$status = new Attribute();
		if(!$status->loadFromCode('srvc', 'tsta', $statusCode))
		{
			$status = new sc\WorkspaceAttribute();
			$status->loadFromCode($ws->getId(), 'tsta', $statusCode);
		}
		
		$lastUpdate = $ticket->getLatestDetail('u');
		$lastLog = $ticket->getLatestDetail('l');
		
		$results['refs'][] = [$ticket->getWorkspace() . "&t=" . $ticket->getNumber()];
		$results['data'][] = [$ws->getName(), $ticket->getNumber(), $ticket->getTitle(), $type->getName(), $category->getName(), $status->getName(), substr($lastUpdate->getData(), 0, 135), sc\getTimeSince($lastLog->getDetailDate())];
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<a class="button" href="<?=SITE_URI?>servicenter/requests/new">New Request</a>
	<select id="requestType">
		<option value="open">My Open Requests</option>
		<option value="closed"<?=ifSet($_GET['r']) == "closed" ? " selected" : ""?>>My Closed Requests</option>
		<option value="all"<?=ifSet($_GET['r']) == "all" ? " selected" : ""?>>All My Requests</option>
	</select>
</div>
<div id="tickets">
	<span class="red-message">NO REQUESTS FOUND</span>
</div>
<script>
	$('#requestType').change(function(){
		var value = $('#requestType').find(":selected").val();
		
		switch(value)
		{
			case "closed":
				window.location = "<?=SITE_URI?>servicenter/requests?r=closed";
				break;
			case "all":
				window.location = "<?=SITE_URI?>servicenter/requests?r=all";
				break;
			case "open":
				window.location = "<?=SITE_URI?>servicenter/requests?r=open";
				break;
		}
	});
	<?php
		if(isset($results) AND !empty($results['data']))
		{
			?>
			showResults('tickets', <?=json_encode($results)?>, <?=RESULTS_PER_PAGE?>);
			<?php
		}
	?>
</script>