<?php
	use servicenter as sc;
	
	// Are ticket details set
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	if(!isset($_GET['t']))
		throw new AppException("Ticket Not Defined", "P03");
	
	// Is Workspace Valid?
	$workspace = new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Not Found", "P04");
	
	// Does user have permission for workspace?
	$userInWorkspace = FALSE;
	
	foreach(sc\getUserWorkspaces() as $ws)
	{
		if($ws->getId() == $workspace->getId())
			$userInWorkspace = TRUE;
	}
	
	if(!$userInWorkspace)
		throw new AppException("You Are Not A Member Of This Workspace", "S01");
	
	// Is Ticket valid
	$ticket = new sc\Ticket();
	if(!$ticket->loadFromNumber($workspace->getId(), $_GET['t']))
		throw new AppException("Ticket Not Found", "P04");
	
	// Load Ticket Attributes
	$category = new sc\WorkspaceAttribute($ticket->getCategory());
	$category->load();
	$type = new sc\WorkspaceAttribute($ticket->getTicketType());
	$type->load();
	$severity = new sc\WorkspaceAttribute($ticket->getSeverity());
	$severity->load();
	
	$createUser = new User($ticket->getCreateUser());
	$createUser->load();
	
	$contactUser = new User($ticket->getContact());
	$contactUser->load();
	
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
	
	$source = new Attribute($ticket->getSource());
	$source->load();
	
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
	
	// Get Ticket History Logs
	$ticketLogs = $ticket->getDetails('l');
	
	$logs['type'] = "description";
	$logs['data'] = [];
	
	foreach($ticketLogs as $log)
	{
		$user = new User($log->getUser());
		$user->load();
		
		$description = [];
		$description['header'] = $user->getFirstName() . " " . $user->getLastName() . " on " . $log->getDetailDate();
		$description['description'] = $log->getData();
		$logs['data'][] = $description;
	}
	
	// Get Linked Tickets
	$linkedTickets = $ticket->getLinkedTickets();
	
	$linked['type'] = "table";
	$linked['linkColumn'] = [0, 5];
	$linked['href'] = [getURI() . "?w=" . $workspace->getId() . "&t=", getURI() . "/../link?w=" . $workspace->getId() . "&t=" . $ticket->getNumber() . "&unlink="];
	$linked['head'] = ['Number', 'Title', 'Link Type', 'Status', 'Last Update', ''];
	$linked['widths'] = ["10px", "", "100px", "150px", "150px", "10px"];
	$linked['align'] = ["center", "left", "right", "right", "right", "center"];
	$linked['data'] = [];
	$linked['refs'] = [];
	
	foreach($linkedTickets as $link)
	{
		$linkedTicket = $link[1];
		
		$linkStatusCode = $linkedTicket->getStatus();
		$linkStatus = new Attribute();
		if(!$linkStatus->loadFromCode('srvc', 'tsta', $linkStatusCode))
		{
			$linkStatus = new sc\WorkspaceAttribute();
			$linkStatus->loadFromCode($workspace->getId(), 'tsta', $statusCode);
		}
		
		$linkLastUpdate = $linkedTicket->getLatestDetail('l');
		
		$linked['data'][] = [$linkedTicket->getNumber(), $linkedTicket->getTitle(), ($link[0] == "d" ? "Dynamic" : "Static"), $linkStatus->getName(), sc\getTimeSince($linkLastUpdate->getDetailDate()), "UNLINK"];
		$linked['refs'][] = [$linkedTicket->getNumber(), $linkedTicket->getNumber()];
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../edit?w=<?=$workspace->getId()?>&t=<?=$ticket->getNumber()?>">Edit</a>
	<a class="button" href="<?=getURI()?>/../link?w=<?=$workspace->getId()?>&t=<?=$ticket->getNumber()?>">Link</a>
	<a class="button" href="<?=getURI()?>/../new?new&w=<?=$workspace->getId()?>">New Ticket</a>
</div>

<h2 class="region-title"><?=$workspace->getName()?> Ticket #<?=$ticket->getNumber()?></h2>
<table class="table-display ticket-display">
	<tr>
		<td>Title</td>
		<td colspan=3><?=htmlentities($ticket->getTitle())?></td>
	</tr>
	<tr>
		<td>Assignees</td>
		<td colspan=3>
		<?php
			$teams = [];
			
			foreach($ticket->getAssignees() as $assigneeId)
			{
				$parts = explode('-', $assigneeId);
				
				if(sizeof($parts) > 0) // Team is present
				{
					if(!isset($teams[$parts[0]])) // Create array for team members if team has not been seen yet
						$teams[$parts[0]] = [];
					
					if(sizeof($parts) > 1) // Member is present
						$teams[$parts[0]][] = $parts[1]; // Add member to team
				}
			}
			
			$tString = "";
			
			foreach(array_keys($teams) as $teamId)
			{
				$team = new sc\Team($teamId);
				if($team->load())
				{
					$tString .= $team->getName();
					
					if(!empty($teams[$teamId]))
					{
						$tString .= " (";
						
						foreach($teams[$teamId] as $userId)
						{
							$user = new User($userId);
							if($user->load())
							{
								$tString .= $user->getFirstName() . " " . $user->getLastName() . ", ";
							}
							
						}
						
						$tString = rtrim($tString, ", ");
						$tString .= ")";
					}
					
					$tString .= ", ";
				}
			}
			
			echo rtrim($tString, ", ");
		?>
		</td>
	</tr>
	<tr>
		<td>Created</td>
		<td><?=$ticket->getCreateDate()?></td>
		<td>By</td>
		<td><?=$createUser->getFirstName() . " " . $createUser->getLastName() . " (" . $createUser->getUsername() . ")"?></td>
	</tr>
</table>
<h2 class="region-title">Contact Information</h2>
<table class="table-display ticket-display">
	<tr>
		<td>Contact Username</td>
		<td><?=$contactUser->getUsername()?></td>
		<td>Contact Name</td>
		<td><?=$contactUser->getFirstName() . " " . $contactUser->getLastName()?></td>
	</tr>
	<tr>
		<td>Contact E-Mail</td>
		<td><?=$contactUser->getEmail()?></td>
	</tr>
</table>
<h2 class="region-title">Ticket Information</h2>
<table class="table-display ticket-display">
	<tr>
		<td>Status</td>
		<td><?=$status->getName()?></td>
		<?php
			if(isset($closureCode))
			{
				?>
				<td>Closure Code</td>
				<td><?=$closureCode->getName()?></td>
				<?php
			}
		?>
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
		<td>Priority</td>
		<td><?=$ticket->getPriority() == 0 ? "Not Set" : $ticket->getPriority()?></td>
	</tr>
	<tr>
		<td>Work Scale</td>
		<td><?=$ticket->getScale() == 0 ? "Not Set" : $ticket->getScale()?></td>
		<td>Source</td>
		<td><?=$source->getName()?></td>
	</tr>
</table>

<h2 class="region-title region-expand region-expand-collapsed" id="additional">Additional Ticket Information</h2>
<table class="region table-display ticket-display" id="additional-region">
	<tr>
		<td>Desired Due Date</td>
		<td><?=$ticket->getDesiredDueDate()?></td>
		<td>Target Date</td>
		<td><?=$ticket->getTargetDate()?></td>
	</tr>
	<tr>
		<td>Work Scheduled Date</td>
		<td><?=$ticket->getWorkScheduleDate()?></td>
		<td>Next Review Date</td>
		<td><?=$ticket->getNextReviewDate()?></td>
	</tr>
	<tr>
		<td>Vendor Info</td>
		<td><?=htmlentities($ticket->getVendorInfo())?></td>
		<td>Location</td>
		<td><?=htmlentities($ticket->getLocation())?></td>
	</tr>
</table>

<h2 class="region-title region-expand region-expand-collapsed" id="description">Description</h2>
<div class="region" id="description-region">
	<span class="red-message">NO DATA FOUND</span>
</div>

<h2 class="region-title region-expand region-expand-collapsed" id="linked">Linked Tickets</h2>
<div class="region" id="linked-region">
	<span class="red-message">NO DATA FOUND</span>
</div>

<h2 class="region-title region-expand region-expand-collapsed" id="history">History</h2>
<div class="region" id="history-region">
	<span class="red-message">NO DATA FOUND</span>
</div>
<?php
	if(isset($updates) AND !empty($updates['data']))
	{
		?><script>showResults('description-region', <?=json_encode($updates)?>, <?=sizeof($updates['data'])?>);</script><?php
	}
	
	if(isset($logs) AND !empty($logs['data']))
	{
		?><script>showResults('history-region', <?=json_encode($logs)?>, <?=sizeof($logs['data'])?>);</script><?php
	}
	
	if(isset($linked) AND !empty($linked['data']))
	{
		?><script>showResults('linked-region', <?=json_encode($linked)?>, <?=sizeof($linked['data'])?>);</script><?php
	}
?>