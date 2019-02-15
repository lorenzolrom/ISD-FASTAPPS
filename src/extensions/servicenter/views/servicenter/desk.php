<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
	{
		// Workspace Select Form
		?>
		<form class="basic-form form">
			<p>
				<span>Workspace</span>
				<select name="w">
				<?php
					foreach(sc\getUserWorkspaces() as $workspace)
					{
					?>
						<option value="<?=$workspace->getId()?>"><?=$workspace->getName()?></option>
					<?php
					}
				?>
				</select>
			</p>
			<input class="button" type="submit" value="Go">
		</form>
		<?php
	}
	else
	{
		// Validate Workspace #/Permissions
		$workspace = new sc\Workspace($_GET['w']);
		
		if(!$workspace->load())
			throw new AppException("Workspace Not Found", "P04");
		
		$userInWorkspace = FALSE;
		
		foreach(sc\getUserWorkspaces() as $ws)
		{
			if($ws->getId() == $workspace->getId())
				$userInWorkspace = TRUE;
		}
		
		if(!$userInWorkspace)
			throw new AppException("You Are Not A Member Of This Workspace", "S01");
		
		$tickets = [];
		
		// Get tickets
		if(isset($_GET['q'])) // Ticket Query
		{
			$tickets = sc\searchTickets($workspace->getId(), wildcard($_GET['q']), wildcard($_GET['q']), wildcard($_GET['q']), wildcard($_GET['q']));
		}
		else if(isset($_GET['t']) AND $_GET['t'] != "assignments")
		{
			// Team/Status Filter
			if($_GET['t'] == "open")
			{
				$tickets = $tickets = sc\getTickets([$workspace->getId()], "%", "%", FALSE, [], [], [], [], [], [], [], [], ['clos']);
			}
			else if($_GET['t'] == "unassigned")
			{
				$tickets = sc\getTicketsByAssignee($workspace->getId());
			}
			else if($_GET['t'] == "closed")
			{
				$tickets = sc\getTickets([$workspace->getId()], "%", "%", FALSE, [], [], [], [], [], [], ['clos']);
			}
			else if($_GET['t'] == "all")
			{
				$tickets = sc\getTickets([$workspace->getId()]);
			}
			else if(substr($_GET['t'], 0, 2) == "t-")
			{
				$vars = explode('-', $_GET['t']);
				if(ifSet($vars[2]) == "a")
					$tickets = sc\getTicketsByAssignee($workspace->getId(), TRUE, ifSet($vars[1]), FALSE, 'clos');
				else if(ifSet($vars[2]) == "u")
					$tickets = sc\getTicketsByAssignee($workspace->getId(), FALSE, ifSet($vars[1]), FALSE, 'clos');
			}
		}
		else
		{
			// Get current user assignments
			$tickets = sc\getTicketsByAssignee($workspace->getId(), $faCurrentUser->getId(), FALSE, FALSE, 'clos');
		}
		
		// Build results array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?w=" . $workspace->getId() . "&t=";
		$results['head'] = ['Number', 'Type', 'Severity', 'Title', 'Status', 'Assignees', 'Last Update'];
		$results['widths'] = ["10px", "10px", "10px", "250px", "150px", "", "100px"];
		$results['align'] = ["center", "left", "left", "left", "left", "right", "right"];
		$results['refs'] = [];
		$results['data'] = [];
		
		foreach($tickets as $ticket)
		{
			$type = new sc\WorkspaceAttribute($ticket->getTicketType());
			$type->load();
			$severity = new sc\WorkspaceAttribute($ticket->getSeverity());
			$severity->load();
			$category = new sc\WorkspaceAttribute($ticket->getCategory());
			$category->load();
			$statusCode = $ticket->getStatus();
			$status = new Attribute();
			if(!$status->loadFromCode('srvc', 'tsta', $statusCode))
			{
				$status = new sc\WorkspaceAttribute();
				$status->loadFromCode($workspace->getId(), 'tsta', $statusCode);
			}
			
			$lastUpdate = $ticket->getLatestDetail('u');
			$lastLog = $ticket->getLatestDetail('l');
			
			// Create assignee string
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
			
			$tString = rtrim($tString, ", ");
			
			$results['refs'][] = [$ticket->getNumber()];
			$results['data'][] = [$ticket->getNumber(), $type->getName(), $severity->getName(), $ticket->getTitle(), $status->getName(), $tString, sc\getTimeSince($lastLog->getDetailDate())];
		}
		
		?>
		<div class="tool-bar">
			<form id="workspace-select">
				<select id="workspace-select-input" name="w">
					<?php
						$workspaces = sc\getUserWorkspaces();
						foreach($workspaces as $ws)
						{
							?>
							<option value="<?=$ws->getId()?>"<?=($workspace->getId() == $ws->getId()) ? " selected" : ""?>><?=$ws->getName()?></option>
							<?php
						}
					?>
				</select>
				<input type="submit" class="button" value="Go">
			</form>
			<form id="search-form">
				<input type="hidden" name="w" value="<?=$workspace->getId()?>">
				<input type="text" name="q" value="<?=ifSet($_GET['q'])?>">
				<span class="button form-submit-button" id="search">Search</span>
				<a class="button" href="<?=getURI()?>/search?w=<?=$workspace->getId()?>">Advanced Search</a>
				<a class="button" href="<?=getURI()?>/new?new&w=<?=$workspace->getId()?>">New Ticket</a>
			</form>
		</div>
		<table class="servicedesk-widgets">
			<tr>
				<?php
					for($i = 1; $i <= 3; $i++)
					{
						$widget = $workspace->getWidget($i);
						if($widget !== FALSE)
						{
						?>
							<td>
								<h3><?=$widget[0]?></h3>
								<table>
									<?php
									foreach($widget[1] as $ticket)
									{
										?>
										<tr>
                                            <td><a href="<?=getURI()?>/view?w=<?=$workspace->getId()?>&t=<?=$ticket->getNumber()?>"><?=$ticket->getNumber()?></a></td>
                                            <td><?=htmlentities($ticket->getTitle())?></td>
                                        </tr>
										<?php
									}
									?>
								</table>
							</td>
						<?php
						}
					}
				?>
			</tr>
		</table>
		<div class="tool-bar">
			<form id="ticket-form">
				<input type="hidden" name="w" value="<?=ifSet($_GET['w'])?>">
				<select id="search-select" name="t">
					<option value="assignments">My Assignments</option>
					<option value="open"<?=(ifSet($_GET['t']) == "open") ? " selected" : ""?>>Open Tickets</option>
					<option value="unassigned"<?=(ifSet($_GET['t']) == "unassigned") ? " selected" : ""?>>Unassigned Tickets</option>
					<option value="closed"<?=(ifSet($_GET['t']) == "closed") ? " selected" : ""?>>Closed Tickets</option>
					<option value="all"<?=(ifSet($_GET['t']) == "all") ? " selected" : ""?>>All Tickets</option>
					<?php
						$teams = sc\getUserTeams($workspace->getId());
						
						foreach($teams as $team)
						{
							?>
							<option value="t-<?=$team->getId()?>-u"<?=(ifSet($_GET['t']) == "t-" . $team->getId() . "-u") ? " selected" : ""?>><?=$team->getName()?> Unassigned</option>
							<option value="t-<?=$team->getId()?>-a"<?=(ifSet($_GET['t']) == "t-" . $team->getId() . "-a") ? " selected" : ""?>><?=$team->getName()?> Assignments</option>
							<?php
						}
						
						if(isset($_GET['q']))
						{
							?>
							<option value="" selected>Current Search Results</option>
							<?php
						}
					?>
				</select>
				<input type="submit" class="button" value="Refresh">
			</form>
		</div>
		<div id="tickets">
			<span class="red-message">NO TICKETS FOUND</span>
		</div>
		<?php
	}
	
	if(isset($results) AND !empty($results['data']))
	{
		?>
		<script>showResults('tickets', <?=json_encode($results)?>, <?=RESULTS_PER_PAGE?>)</script>
		<?php
	}
?>
<script>
	$('#search-select').change(function(){
		$('#search-select').parent().submit();
	});
	
	$('#workspace-select-input').change(function(){
		$('#workspace-select-input').parent().submit();
	});
</script>