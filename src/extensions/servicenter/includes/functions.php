<?php namespace servicenter;
	////////////////////
	// ISD-FastApps
	// (c) 2018 LLR Technologies / Info. Systems Development
	// ServiCenter Extension Function File
	////////////////////

	/**
	* Returns a formatted string representing the time since the indicated start
	* @param start The origin time
	*/
	function getTimeSince($start)
	{
		$start_date = new \DateTime($start);
		$since_start = $start_date->diff(new \DateTime(date("Y-m-d H:i:s")));
		
		$years = $since_start->y.' yr ';
		$months = $since_start->m.' mos ';
		$days = $since_start->d.' days ';
		$hours = $since_start->h.' hrs ';
		$minutes = $since_start->i.' min ';
		
		$formattedDate = "";
		
		if($years != 0)
			$formattedDate .= $years;
		if($months != 0)
			$formattedDate .= $months;
		if($years == 0 AND $days != 0)
			$formattedDate .= $days;
		if($years == 0 AND $months == 0 AND $hours != 0)
			$formattedDate .= $hours;
		if($years == 0 AND $months == 0 AND $days == 0 AND $minutes != 0)
			$formattedDate .= $minutes;
		
		if(empty($formattedDate))
			$formattedDate .= "now";
		else
			$formattedDate .= "ago";
		
		return $formattedDate;
	}
	
	/**
	* Get the next ticket number for the workspace
	*/
	function getNextTicketNumber($workspaceId)
	{
		global $conn;
		
		$g = $conn->prepare("SELECT number FROM ServiCenter_Ticket WHERE workspace = ? ORDER BY number DESC LIMIT 1");
		$g->bindParam(1, $workspaceId);
		$g->execute();
		
		if($g->rowCount() == 0)
			return 1;
		
		return $g->fetchColumn() + 1;
	}
	
	/**
	* @return Array of Team objects matching filters
	*/
	function getTeams($nameFilter = "%")
	{
		global $conn;
		
		$g = $conn->prepare("SELECT id FROM ServiCenter_Team WHERE name LIKE ? ORDER BY name ASC");
		$g->bindParam(1, $nameFilter);
		$g->execute();
		
		$ts = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$t = new Team($id);
			if($t->load())
				$ts[] = $t;
		}
		
		return $ts;
	}
	
	/**
	* @return Array of Workspace objects matching filters
	*/
	function getWorkspaces($nameFilter = "%")
	{
		global $conn;
		
		$g = $conn->prepare("SELECT id FROM ServiCenter_Workspace WHERE name LIKE ? ORDER BY name ASC");
		$g->bindParam(1, $nameFilter);
		$g->execute();
		
		$ws = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$w = new Workspace($id);
			if($w->load())
				$ws[] = $w;
		}
		
		return $ws;
	}
	
	/**
	* @return The default workspace, or FALSE if there is none
	*/
	function getDefaultWorkspace()
	{
		global $conn;
		
		$g = $conn->prepare("SELECT id FROM ServiCenter_Workspace WHERE `default` = 1 LIMIT 1");
		$g->execute();
		
		if($g->rowCount() == 0)
			return FALSE;
		
		$w = new Workspace($g->fetchColumn());
		
		if($w->load())
			return $w;
		
		return FALSE;
	}
	
	function getTickets($workspaceFilter = [], $numberFilter = "%", $titleFilter = "%", $contactUsername = FALSE, $severityFilter = [], $priorityFilter = [], $scaleFilter = [], 
		$typeFilter = [], $categoryFilter = [], $sourceFilter = [], $statusCodeFilter = [], $closureCodeFilter = [], $notStatusFilter = [])
	{
		global $conn;
		
		$q = ("SELECT ServiCenter_Ticket.id FROM ServiCenter_Ticket INNER JOIN ServiCenter_TicketDetail ON ServiCenter_Ticket.id = ticket 
			WHERE number LIKE ? AND title LIKE ?");
		
		if($contactUsername !== FALSE)
			$q .= " AND contact IN (SELECT id FROM User WHERE username = ?)";
		
		// Workspace Filter
		if(!empty($workspaceFilter))
		{
			$l = [];
			foreach($workspaceFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND workspace IN ('$s')";
			}
		}
		
		// Severity Filter
		if(!empty($severityFilter))
		{
			$l = [];
			foreach($severityFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND severity IN ('$s')";
			}
		}
		
		// Priority Filter
		if(!empty($priorityFilter))
		{
			$l = [];
			foreach($priorityFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND priority IN ('$s')";
			}
		}
		
		// Scale Filter
		if(!empty($scaleFilter))
		{
			$l = [];
			foreach($scaleFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND scale IN ('$s')";
			}
		}
		
		// Type Filter
		if(!empty($typeFilter))
		{
			$l = [];
			foreach($typeFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND type IN ('$s')";
			}
		}
		
		// Category Filter
		if(!empty($categoryFilter))
		{
			$l = [];
			foreach($categoryFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND category IN ('$s')";
			}
		}
		
		// Source Filter
		if(!empty($sourceFilter))
		{
			$l = [];
			foreach($sourceFilter as $i)
			{
				if(ctype_digit($i))
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND source IN ('$s')";
			}
		}
		
		// Status Code Filter
		if(!empty($statusCodeFilter))
		{
			$l = [];
			foreach($statusCodeFilter as $i)
			{
				if(strlen($i) == 4)
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND (status IN (SELECT code FROM Attribute WHERE extension = 'srvc' AND `type` = 'tsta' AND code IN ('$s')) OR 
					status IN (SELECT code FROM ServiCenter_TicketAttribute WHERE `type` = 'tsta' AND code IN ('$s')))";
			}
		}
		
		// Closure Code Filter
		if(!empty($closureCodeFilter))
		{
			$l = [];
			foreach($closureCodeFilter as $i)
			{
				if(strlen($i) == 4)
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND status IN (SELECT code FROM Attribute WHERE extension = 'srvc' AND `type` = 'tclc' AND code IN ('$s'))";
			}
		}
		
		// Not status filter
		if(!empty($notStatusFilter))
		{
			$l = [];
			foreach($notStatusFilter as $i)
			{
				if(strlen($i) == 4)
					$l[] = $i;
				
				$s = implode("', '", $l);
				$q .= " AND status NOT IN (SELECT code FROM Attribute WHERE extension = 'srvc' AND `type` = 'tsta' AND code IN ('$s')) AND
					status NOT IN (SELECT code FROM ServiCenter_TicketAttribute WHERE `type` = 'tsta' AND code IN ('$s'))";
			}
		}
		
		$q .= " GROUP BY ServiCenter_Ticket.id ORDER BY MAX(date) DESC";
		
		$g = $conn->prepare($q);
		$g->bindParam(1, $numberFilter);
		$g->bindParam(2, $titleFilter);
		if($contactUsername !== FALSE)
			$g->bindParam(3, $contactUsername);
		$g->execute();
		
		$ts = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $i)
		{
			$t = new Ticket($i);
			if($t->load())
				$ts[] = $t;
		}
		
		return $ts;
	}
	
	/**
	* Get tickets by assigned user/team
	* @param $assigneeId Numerical ID of assigned user
	* @param $teamId Numerical ID of assigned team
	* @param $status = Ticket status to get
	* @param $notStatus = Ticket status to ignore
	*/
	function getTicketsByAssignee($workspace = FALSE, $assigneeId = FALSE, $teamId = FALSE, $status = FALSE, $notStatus = FALSE)
	{
		global $conn;
		
		if($workspace === FALSE)
			return FALSE;
		
		$q = "SELECT ServiCenter_Ticket_Assignee.ticket FROM ServiCenter_Ticket_Assignee 
			INNER JOIN ServiCenter_TicketDetail ON ServiCenter_TicketDetail.ticket = ServiCenter_Ticket_Assignee.ticket 
			INNER JOIN ServiCenter_Ticket ON ServiCenter_Ticket.id = ServiCenter_Ticket_Assignee.ticket 
			WHERE workspace = ?";
		
		if($teamId === FALSE AND $assigneeId === FALSE) // All unassigned tickets
		{
			$q = "SELECT ServiCenter_Ticket.id FROM ServiCenter_Ticket 
				INNER JOIN ServiCenter_TicketDetail ON ServiCenter_Ticket.id = ServiCenter_TicketDetail.ticket 
				WHERE ServiCenter_Ticket.workspace = ? AND ServiCenter_Ticket.id NOT IN (SELECT ticket FROM ServiCenter_Ticket_Assignee)";
		}
		else if($teamId !== FALSE AND $assigneeId === FALSE) // Get tickets assigned to team but not user
			$q .= " AND team = ? AND ServiCenter_Ticket_Assignee.user IS NULL";
		else if($teamId !== FALSE AND $assigneeId === TRUE) // Get tickets assigned to team and and user in team
			$q .= " AND team = ? AND ServiCenter_Ticket_Assignee.user IS NOT NULL";
		else if($assigneeId !== FALSE) // Get tickets assigned to specified user
			$q .= " AND ServiCenter_Ticket_Assignee.user = ?";
		else
			return FALSE;
		
		if($status !== FALSE)
			$q .= " AND status = ?";
		else if($notStatus !== FALSE)
			$q .= " AND status != ?";
		
		$q .= " GROUP BY ticket ORDER BY MAX(ServiCenter_TicketDetail.date) DESC";
		
		$get = $conn->prepare($q);
		
		$get->bindParam(1, $workspace);
		
		if($teamId !== FALSE)
			$get->bindParam(2, $teamId);
		else if($assigneeId !== FALSE)
			$get->bindParam(2, $assigneeId);
		
		if($status !== FALSE)
			$get->bindParam(3, $status);
		else if($notStatus !== FALSE)
			$get->bindParam(3, $notStatus);
		
		$get->execute();
		
		$tickets = [];
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$ticket = new Ticket($id);
			if($ticket->load())
				$tickets[] = $ticket;
		}
		
		return $tickets;
	}
	
	/**
	* Search for tickets, with less strict requirements than getTickets()
	*/
	function searchTickets($workspace, $numberFilter = "%", $titleFilter = "%", $contactUsername = "%", $descriptionFilter = "%")
	{
		global $conn;
		
		$g = $conn->prepare("SELECT id FROM ServiCenter_Ticket WHERE workspace = ? AND 
			(number LIKE ? OR title LIKE ? OR id IN (SELECT ticket FROM ServiCenter_TicketDetail WHERE `type` = 'u' AND data LIKE ?) 
			OR IFNULL(contact, '') IN (SELECT id FROM User WHERE username LIKE ?)) ORDER BY id DESC");
			
		$g->bindParam(1, $workspace);
		$g->bindParam(2, $numberFilter);
		$g->bindParam(3, $titleFilter);
		$g->bindParam(4, $descriptionFilter);
		$g->bindParam(5, $contactUsername);
		
		$g->execute();
		
		$tickets = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$ticket = new Ticket($id);
			if($ticket->load())
				$tickets[] = $ticket;
		}
		
		return $tickets;
	}
	
	/**
	* Get workspaces for the supplied user
	* @return Array of workspaces
	*/
	function getUserWorkspaces()
	{
		global $conn;
		
		// Get teams
		$teams = getUserTeams();
		
		// Convert teams array to array of team IDs
		$tids = [];
		
		foreach($teams as $team)
		{
			$tids[] = $team->getId();
		}
		
		$tids = implode("', '", $tids);
		
		// Find workspaces that are assigned to teams
		$get = $conn->prepare("SELECT workspace from ServiCenter_Team_Workspace WHERE team IN ('$tids')");
		$get->execute();
		
		$workspaceIds = [];
		$workspaces = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$workspace = new Workspace($id);
			if($workspace->load() AND !in_array($workspace->getId(), $workspaceIds))
			{
				$workspaceIds[] = $workspace->getId();
				$workspaces[] = $workspace;
			}
		}
		return $workspaces;
	}
	
	/**
	* Get teams for the supplied user
	* @return Array of Teams
	*/
	function getUserTeams($workspace = FALSE)
	{
		global $conn;
		global $faCurrentUser;
		
		$q = "SELECT team FROM ServiCenter_Team_User WHERE user = ?";
		
		if($workspace !== FALSE)
			$q .= " AND team IN (SELECT team FROM ServiCenter_Team_Workspace WHERE workspace = ?)";
		
		$get = $conn->prepare($q);
		$get->bindParam(1, $faCurrentUser->getId());
		
		if($workspace !== FALSE)
			$get->bindParam(2, $workspace);
		
		$get->execute();
		
		$teams = [];
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$team = new Team($id);
			if($team->load())
				$teams[] = $team;
		}
		
		return $teams;
	}
