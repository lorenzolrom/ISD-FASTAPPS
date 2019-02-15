<?php namespace servicenter;

	/**
	* Ticket
	*/
	class Ticket
	{
		private $id; // Row identified
		private $workspace; // Workspace this ticket belonds to
		private $number; // Ticket number in workspace
		private $title; // Ticket title
		private $contact; // Id of contact user (optional)
		private $createUser; // User who created ticket
		
		private $severity; // Severity
		private $priority; // Internal priority
		private $scale; // Internal scale of work
		private $type; // Type of request
		private $category; // Category
		private $source; // Source of ticket
		private $status; // Ticket status
		private $closureCode; // Ticket closure code (optional unless status is closed)
		
		private $desiredDueDate; // Desired due date (optional)
		private $nextReviewDate; // Next review date (optional)
		private $workScheduleDate; // Work scheduled (optional)
		private $targetDate; // Target date for completion (optional)
		private $createDate; // Date/Time ticket was created
		
		private $vendorInfo;
		private $location;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getWorkspace(){return $this->workspace;}
		public function getNumber(){return $this->number;}
		public function getTitle(){return $this->title;}
		public function getContact(){return $this->contact;}
		public function getCreateUser(){return $this->createUser;}
		
		public function getSeverity(){return $this->severity;}
		public function getPriority(){return $this->priority;}
		public function getScale(){return $this->scale;}
		public function getTicketType(){return $this->type;}
		public function getCategory(){return $this->category;}
		public function getSource(){return $this->source;}
		public function getStatus(){return $this->status;}
		public function getClosureCode(){return $this->closureCode;}
		
		public function getDesiredDueDate(){return $this->desiredDueDate;}
		public function getNextReviewDate(){return $this->nextReviewDate;}
		public function getWorkScheduleDate(){return $this->workScheduleDate;}
		public function getTargetDate(){return $this->targetDate;}
		public function getCreateDate(){return $this->createDate;}
		
		public function getVendorInfo(){return $this->vendorInfo;}
		public function getLocation(){return $this->location;}
		
		public function setId($id){$this->id = $id;}
		public function setWorkspace($workspace){$this->workspace = $workspace;}
		public function setNumber($number){$this->number = $number;}
		public function setTitle($title){$this->title = $title;}
		public function setContact($contact){$this->contact = $contact;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		
		public function setSeverity($severity){$this->severity = $severity;}
		public function setPriority($priority){$this->priority = $priority;}
		public function setScale($scale){$this->scale = $scale;}
		public function setTicketType($type){$this->type = $type;}
		public function setCategory($category){$this->category = $category;}
		public function setSource($source){$this->source = $source;}
		public function setStatus($status){$this->status = $status;}
		public function setClosureCode($closureCode){$this->closureCode = $closureCode;}
		
		public function setDesiredDueDate($desiredDueDate){$this->desiredDueDate = $desiredDueDate;}
		public function setNextReviewDate($nextReviewDate){$this->nextReviewDate = $nextReviewDate;}
		public function setWorkScheduleDate($workScheduleDate){$this->workScheduleDate = $workScheduleDate;}
		public function setTargetDate($targetDate){$this->targetDate = $targetDate;}
		public function setCreateDate($createDate){$this->createDate = $createDate;}
		
		public function setVendorInfo($vendorInfo){$this->vendorInfo = $vendorInfo;}
		public function setLocation($location){$this->location = $location;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT workspace, `number`, title, contact, severity, priority, scale, `type`, category, 
				source, status, closureCode, desiredDueDate, nextReviewDate, workScheduleDate, targetDate, 
				vendorInfo, location, createUser, createDate FROM ServiCenter_Ticket WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$t = $f->fetch();
			
			$this->workspace = $t['workspace'];
			$this->number = $t['number'];
			$this->title = $t['title'];
			$this->contact = $t['contact'];
			$this->severity = $t['severity'];
			$this->priority = $t['priority'];
			$this->scale = $t['scale'];
			$this->type = $t['type'];
			$this->category = $t['category'];
			$this->source = $t['source'];
			$this->status = $t['status'];
			$this->closureCode = $t['closureCode'];
			$this->desiredDueDate = $t['desiredDueDate'];
			$this->nextReviewDate = $t['nextReviewDate'];
			$this->workScheduleDate = $t['workScheduleDate'];
			$this->targetDate = $t['targetDate'];
			$this->vendorInfo = $t['vendorInfo'];
			$this->location = $t['location'];
			$this->createUser = $t['createUser'];
			$this->createDate = $t['createDate'];
			
			return TRUE;
		}
		
		private function fetchFromNumber()
		{
			if(!isset($this->number) OR !isset($this->workspace))
				RETURN FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ServiCenter_Ticket WHERE workspace = ? AND number = ? LIMIT 1");
			$f->bindParam(1, $this->workspace);
			$f->bindParam(2, $this->number);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ServiCenter_Ticket (workspace, `number`, contact, severity, priority, 
				scale, `type`, category, source, status, closureCode, desiredDueDate, nextReviewDate, workScheduleDate, 
				targetDate, vendorInfo, location, createUser, createDate, title) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
				?, ?, ?, ?, ?, NOW(), ?)");
			$p->bindParam(1, $this->workspace);
			$p->bindParam(2, $this->number);
			$p->bindParam(3, $this->contact);
			$p->bindParam(4, $this->severity);
			$p->bindParam(5, $this->priority);
			$p->bindParam(6, $this->scale);
			$p->bindParam(7, $this->type);
			$p->bindParam(8, $this->category);
			$p->bindParam(9, $this->source);
			$p->bindParam(10, $this->status);
			$p->bindParam(11, $this->closureCode);
			$p->bindParam(12, $this->desiredDueDate);
			$p->bindParam(13, $this->nextReviewDate);
			$p->bindParam(14, $this->workScheduleDate);
			$p->bindParam(15, $this->targetDate);
			$p->bindParam(16, $this->vendorInfo);
			$p->bindParam(17, $this->location);
			$p->bindParam(18, $this->createUser);
			$p->bindParam(19, $this->title);
			
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
		}
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$p = $conn->prepare("UPDATE ServiCenter_Ticket SET contact = ?, severity = ?, 
				priority = ?, scale = ?, `type` = ?, category = ?, source = ?, status = ?, closureCode = ?, 
				desiredDueDate = ?, nextReviewDate = ?, workScheduleDate = ?, targetDate = ?, vendorInfo = ?, 
				location = ?, title = ? WHERE id = ?");
			$p->bindParam(1, $this->contact);
			$p->bindParam(2, $this->severity);
			$p->bindParam(3, $this->priority);
			$p->bindParam(4, $this->scale);
			$p->bindParam(5, $this->type);
			$p->bindParam(6, $this->category);
			$p->bindParam(7, $this->source);
			$p->bindParam(8, $this->status);
			$p->bindParam(9, $this->closureCode);
			$p->bindParam(10, $this->desiredDueDate);
			$p->bindParam(11, $this->nextReviewDate);
			$p->bindParam(12, $this->workScheduleDate);
			$p->bindParam(13, $this->targetDate);
			$p->bindParam(14, $this->vendorInfo);
			$p->bindParam(15, $this->location);
			$p->bindParam(16, $this->title);
			$p->bindParam(17, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		
		public function loadFromNumber($workspace = FALSE, $number = FALSE)
		{
			if($workspace !== FALSE)
				$this->workspace = $workspace;
			if($number !== FALSE)
				$this->number = $number;
			
			return $this->fetchFromNumber();
		}
		
		/**
		* @return Array of Ticket objets linked to this ticket
		* @param $type Specify the type of link to check for (optional)
		*	This parameter only supports 's' and 'd'
		*/
		public function getLinkedTickets($type = FALSE)
		{
			if(!isset($this->id))
				return [];
			
			global $conn;
			
			$q = "SELECT ticket1, ticket2, linkType FROM ServiCenter_TicketLink WHERE ticket1 = :ticket OR ticket2 = :ticket";
			
			// Are we filtering by link type?
			if($type !== FALSE)
			{
				switch($type)
				{
					case 's':
						$q .= " AND linkType = 's'";
						break;
					case 'd':
						$q .= " AND linkType = 'd'";
						break;
				}
			}
			
			// Execute search
			$g = $conn->prepare($q);
			$g->bindParam(':ticket', $this->id);
			$g->execute();
			
			$tickets = [];
			
			foreach($g->fetchAll() as $link)
			{
				$id = ($this->id == $link['ticket1']) ? $link['ticket2'] : $link['ticket1'];
				
				$ticket = new Ticket($id);
				
				if($ticket->load())
					$tickets[] = [$link['linkType'], $ticket];
			}
			
			return $tickets;
		}
		
		/**
		* Link another ticket to this one
		* @param $ticketId Numerical ID of ticket to link
		* @param $type Link type, s = static, d = dynamic, defaults to static
		*/
		public function linkTicket($ticketId, $type = 's')
		{			
			if(!isset($this->id))
				return ['Ticket # Required'];
			
			if(!in_array($type, ['s', 'd']))
				return ['Link Type Is Invalid'];
			
			global $conn;
			global $faCurrentUser;
			
			// Validate ticket to link
			$t = new Ticket($ticketId);
			if(!$t->load())
				return ['Ticket # Not Found'];
			
			// Validate ticket is not already linked
			$c = $conn->prepare("SELECT ticket1 FROM ServiCenter_TicketLink WHERE (ticket1 = :ticket1 AND ticket2 = :ticket2) OR (ticket2 = :ticket1 AND ticket2 = :ticket2) LIMIT 1");
			$c->bindParam(':ticket1', $this->id);
			$c->bindParam(':ticket2', $ticketId);
			$c->execute();
			
			$conn->beginTransaction();
			
			// Link ticket
			if($c->rowCount() == 1)
				return ['Ticket Already Linked'];
			
			$l = $conn->prepare("INSERT INTO ServiCenter_TicketLink (ticket1, ticket2, linkType) VALUE (?, ?, ?)");
			$l->bindParam(1, $this->id);
			$l->bindParam(2, $ticketId);
			$l->bindParam(3, $type);
			$l->execute();
			
			if($l->rowCount() != 1)
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create log
			$log = new TicketDetail();
			$log->setTicket($this->id);
			$log->setDetailType('l');
			$log->setUser($faCurrentUser->getId());
			$log->setData("Linked To Ticket #" . $t->getNumber());
			
			if(!$log->create())
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Un-links a ticket from this one
		* @param $ticketId Numerical ID of ticket to un-link
		*/
		public function unlinkTicket($ticketId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			global $faCurrentUser;
			
			$u = $conn->prepare("DELETE FROM ServiCenter_TicketLink WHERE (ticket1 = :ticket1 AND ticket2 = :ticket2) OR (ticket2 = :ticket1 AND ticket2 = :ticket2)");
			$u->bindparam(':ticket1', $this->id);
			$u->bindParam(':ticket2', $ticketId);
			$u->execute();
			
			$conn->beginTransaction();
			
			// Unlink ticket
			if($u->rowCount() != 1)
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create log
			$t = new Ticket($ticketId); // Load in ticket being unlinked to reference number
			$t->load();
			
			$log = new TicketDetail();
			$log->setTicket($this->id);
			$log->setDetailType('l');
			$log->setUser($faCurrentUser->getId());
			$log->setData("Un-Linked From Ticket #" . $t->getNumber());
			
			if(!$log->create())
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Assign team/user to this ticket
		*/
		public function assign($teamId = FALSE, $userId = FALSE)
		{
			global $conn;
			
			if($teamId !== FALSE)
			{
				// Verify team
				$t = new Team($teamId);
				if(!$t->load())
					return FALSE;
			}
			
			if($userId !== FALSE)
			{
				// Verify user
				$u = new \User($userId);
				if(!$u->load())
					return FALSE;
			}
			
			if($teamId !== FALSE AND $userId === FALSE) // Assign team only
			{
				// Verify team is not assigned already
				$c = $conn->prepare("SELECT ticket FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND team = ? AND user IS NULL LIMIT 1");
				$c->bindParam(1, $this->id);
				$c->bindParam(2, $teamId);
				$c->execute();
				
				if($c->rowCount() == 1)
					return FALSE;
				
				$a = $conn->prepare("INSERT INTO ServiCenter_Ticket_Assignee (ticket, team) VALUES (?, ?)");
				$a->bindParam(1, $this->id);
				$a->bindParam(2, $teamId);
				$a->execute();
				
				if($a->rowCount() == 1)
					return TRUE;
				
				return FALSE;
			}
			else if($teamId === FALSE AND $userId !== FALSE) // Assign user only
			{
				// Verify user is not assigned already
				$c = $conn->prepare("SELECT ticket FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND user = ? AND team IS NULL LIMIT 1");
				$c->bindParam(1, $this->id);
				$c->bindParam(2, $userId);
				$c->execute();
				
				if($c->rowCount() == 1)
					return FALSE;
				
				$a = $conn->prepare("INSERT INTO ServiCenter_Ticket_Assignee (ticket, user) VALUES (?, ?)");
				$a->bindParam(1, $this->id);
				$a->bindParam(2, $userId);
				$a->execute();
				
				if($a->rowCount() == 1)
					return TRUE;
				
				return FALSE;
			}
			else if($teamId !== FALSE AND $userId !== FALSE) // Assign team/user
			{
				// Verify team/user is not already assigned
				$c = $conn->prepare("SELECT ticket FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND user = ? AND team = ? LIMIT 1");
				$c->bindParam(1, $this->id);
				$c->bindParam(2, $userId);
				$c->bindParam(3, $teamId);
				$c->execute();
				
				if($c->rowCount() == 1)
					return FALSE;
				
				// Remove entry for team only (if exists)
				$d = $conn->prepare("DELETE FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND team = ? AND user IS NULL");
				$d->bindParam(1, $this->id);
				$d->bindParam(2, $teamId);
				$d->execute();
				
				$a = $conn->prepare("INSERT INTO ServiCenter_Ticket_Assignee (ticket, user, team) VALUES (?, ?, ?)");
				$a->bindParam(1, $this->id);
				$a->bindParam(2, $userId);
				$a->bindParam(3, $teamId);
				$a->execute();
				
				if($a->rowCount() == 1)
					return TRUE;
				
				return FALSE;
			}
			else
				return FALSE;
		}
		
		/**
		* De-assign team/user from this ticket
		*/
		public function deassign($teamId = FALSE, $userId = FALSE)
		{
			global $conn;
			
			if($teamId !== FALSE AND $userId == FALSE) // Remove team only (and all members in that team)
			{
				$d = $conn->prepare("DELETE FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND team = ?");
				$d->bindParam(1, $this->id);
				$d->bindParam(2, $teamId);
				$d->execute();
				
				if($d->rowCount() == 0)
					return FALSE;
				return TRUE;
			}
			else if($teamId === FALSE AND $userId !== FALSE) // Remove user only (not under team)
			{
				$d = $conn->prepare("DELETE FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND user = ? AND team IS NULL");
				$d->bindParam(1, $this->id);
				$d->bindParam(2, $userId);
				$d->execute();
				
				if($d->rowCount() == 0)
					return FALSE;
				return TRUE;
			}
			else if($teamId !== FALSE AND $userId !== FALSE) // Remove user under team
			{
				$d = $conn->prepare("DELETE FROM ServiCenter_Ticket_Assignee WHERE ticket = ? AND user = ? AND team = ?");
				$d->bindParam(1, $this->id);
				$d->bindParam(2, $userId);
				$d->bindParam(3, $teamId);
				$d->execute();
				
				if($d->rowCount() == 0)
					return FALSE;
				
				return TRUE;
			}
			else
				return FALSE;
		}
		
		/**
		* Returns a list of team/member id's (formatted) that are assigned to this ticket
		*/
		public function getAssignees()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT team,user FROM ServiCenter_Ticket_Assignee WHERE ticket = ? ORDER BY team ASC");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$assignees = [];
			
			foreach($get->fetchAll() as $assignee)
			{
				$assigneeString = $assignee['team'];
				
				if($assignee['user'] !== NULL)
					$assigneeString .= "-" . $assignee['user'];
				
				$assignees[] = $assigneeString;
			}
			
			return $assignees;
		}
		
		/**
		* Get most recent TicketDetail object for this ticket of the specified type
		*/
		public function getLatestDetail($type = 'u')
		{
			global $conn;
			$get = $conn->prepare("SELECT id FROM ServiCenter_TicketDetail WHERE ticket = ? AND type = ? ORDER BY date DESC LIMIT 1");
			$get->bindParam(1, $this->id);
			$get->bindParam(2, $type);
			$get->execute();
			
			if($get->rowCount() == 0)
				return FALSE;
			
			$d = new TicketDetail($get->fetchColumn());
			if($d->load())
				return $d;
			return FALSE;
		}
		
		/**
		* @return Array of TicketDetails for this ticket of the specified type
		*/
		public function getDetails($type = 'u')
		{
			global $conn;
			$get = $conn->prepare("SELECT id FROM ServiCenter_TicketDetail WHERE ticket = ? AND type = ? ORDER BY date DESC");
			$get->bindParam(1, $this->id);
			$get->bindParam(2, $type);
			$get->execute();
			
			$ds = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$d = new TicketDetail($id);
				if($d->load())
					$ds[] = $d;
			}
			
			return $ds;
		}
		
		/**
		* Validates and updates ticket attributes given array
		* @param $vars Array of attributes
		* @return Array if errors, log entry if successful,
		* false if $vars is empty or not an array
		*/
		private function update($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new \Validator();
			$workspace = new Workspace();
			$errs = [];
			
			// Workspace - is present, exists, user has permission
			if(!isset($vars['workspace']))
				$errs[] = "Workspace Not Defined";
			else
			{
				$workspace->setId($vars['workspace']);
				if(!$workspace->load())
					$errs[] = "Workspace Not Found";
				else
				{
					$userInWorkspace = FALSE;
		
					foreach(getUserWorkspaces() as $ws)
					{
						if($ws->getId() == $workspace->getId())
							$userInWorkspace = TRUE;
					}
					
					if(!$userInWorkspace)
						$errs[] = "Cannot Create Ticket In Specified Workspace";
				}
			}
			
			// Title - is present, is not greater than 64 chars
			if(!isset($vars['title']) OR strlen($vars['title']) < 1 OR strlen($vars['title']) > 64)
				$errs[] = "Title Must Be Between 1 And 64 Characters";
			
			// Contact Username (optional) - exists
			$user = new \User();
			
			if(isset($vars['contactUsername']) AND strlen($vars['contactUsername']) > 0)
			{
				if(!$user->loadFromUsername($vars['contactUsername']))
					$errs[] = "Contact Username Not Found";
			}
			
			// Status - is present, is valid
			$status = new \Attribute();
			$wStatus = new WorkspaceAttribute();
			
			if(!isset($vars['status']) OR strlen($vars['status']) == 0)
				$errs[] = "Status Required";
			else if(!$status->loadFromCode('srvc', 'tsta', $vars['status']) AND !$wStatus->loadFromCode($workspace->getId(), 'tsta', $vars['status']))
				$errs[] = "Status Is Invalid";
			
			// Closure Code - if status is 'clos', is required, is valid
			if(isset($vars['status']) AND $vars['status'] == 'clos')
			{
				$closureCode = new \Attribute();
				$wClosureCode = new WorkspaceAttribute();
				
				if(!isset($vars['closureCode']) OR strlen($vars['closureCode']) == 0)
					$errs[] = "Closure Code Required";
				else if(!$closureCode->loadFromCode('srvc', 'tclc', $vars['closureCode']) AND !$wClosureCode->loadFromCode($workspace->getId(), 'tclc', $vars['closureCode']))
					$errs[] = "Closure Code Is Invalid";
			}
			
			// Type - is present, is valid
			$type = new WorkspaceAttribute();
			
			if(!isset($vars['type']) OR strlen($vars['type']) == 0)
				$errs[] = "Type Required";
			else if(!$type->loadFromCode($workspace->getId(), 'type', $vars['type']))
				$errs[] = "Type Is Invalid";
			
			// Category - is present, is valid
			$category = new WorkspaceAttribute();
			
			if(!isset($vars['category']) OR strlen($vars['category']) == 0)
				$errs[] = "Category Required";
			else if(!$category->loadFromCode($workspace->getId(), 'cate', $vars['category']))
				$errs[] = "Category Is Invalid";
			
			// Severity - is present, is valid
			$severity = new WorkspaceAttribute();
			
			if(!isset($vars['severity']) OR strlen($vars['severity']) == 0)
				$errs[] = "Severity Required";
			else if(!$severity->loadFromCode($workspace->getId(), 'seve', $vars['severity']))
				$errs[] = "Severity Is Invalid";
			
			// Priority (optional) - is number, is within range
			if(isset($vars['priority']) AND !ctype_digit($vars['priority']))
				$errs[] = "Priority Is Invalid";
			else if($vars['priority'] > $workspace->getPriorityLevels())
				$errs[] = "Priority Is Invalid";
			
			// Scale (optional) - is number, is within range			
			if(isset($vars['workScale']) AND !ctype_digit($vars['workScale']))
				$errs[] = "Work Scale Is Invalid";
			else if($vars['workScale'] > $workspace->getScaleLevels())
				$errs[] = "Work Scale Is Invalid";
			
			// Source - is present, is valid
			$source = new \Attribute();
			
			if(!isset($vars['source']) OR strlen($vars['source']) == 0)
				$errs[] = "Source Required";
			else
			{
				$source = new \Attribute();
				if(!$source->loadFromCode('srvc', 'tsrc', $vars['source']))
					$errs[] = "Source Is Invalid";
			}
			
			// Desired Due Date (optional) - is date
			if(isset($vars['desiredDueDate']) AND strlen($vars['desiredDueDate']) != 0 AND !$validator->validDate($vars['desiredDueDate']))
				$errs[] = "Desired Due Date Is Invalid";
			else if(strlen($vars['desiredDueDate']) == 0)
				$vars['desiredDueDate'] = null;
			
			// Target Date (optional) - is date
			if(isset($vars['targetDate']) AND strlen($vars['targetDate']) != 0 AND !$validator->validDate($vars['targetDate']))
				$errs[] = "Target Date Is Invalid";
			else if(strlen($vars['targetDate']) == 0)
				$vars['targetDate'] = null;
			
			// Work Scheduled Date (optional) - is date
			if(isset($vars['workScheduleDate']) AND strlen($vars['workScheduleDate']) != 0 AND !$validator->validDate($vars['workScheduleDate']))
				$errs[] = "Work Scheduled Date Is Invalid";
			else if(strlen($vars['workScheduleDate']) == 0)
				$vars['workScheduleDate'] = null;
			
			// Next Review Date (optional) - is date
			if(isset($vars['nextReviewDate']) AND strlen($vars['nextReviewDate']) != 0 AND !$validator->validDate($vars['nextReviewDate']))
				$errs[] = "Next Review Date Is Invalid";
			else if(strlen($vars['nextReviewDate']) == 0)
				$vars['nextReviewDate'] = null;
			
			if(!empty($errs))
				return $errs;
			
			/////
			// CREATE LOG
			/////
			
			$log = new TicketDetail();
			$log->setDetailType('l');
			
			// Compare new and old values
			$logString = "";
			
			// Title
			if($this->title != $vars['title'])
				$logString .= "Title set to '" . $vars['title'] . "' from '" . $this->title . "'\n";
			
			// Contact
			if(isset($user) AND $user->getId() != $this->contact)
			{
				$oldContact = new \User($this->contact);
				$oldContact->load();
				$logString .= "Contact set to '" . $vars['contactUsername'] . "' from '" . $oldContact->getUsername() . "'\n";
			}
			
			// Status
			if($this->status != $vars['status'])
			{
				$oldStatus = null;
				
				$oldStatus = new \Attribute();
				if(!$oldStatus->loadFromCode('srvc', 'tsta', $this->status))
				{
					$oldStatus = new WorkspaceAttribute();
					$oldStatus->loadFromCode($workspace->getId(), 'tsta', $this->status);
				}
				
				$logString .= "Status set to '" . ($status->getName() !== NULL ? $status->getName() : $wStatus->getName()) . "' from '" . $oldStatus->getName() . "'\n";
			}
			
			// Closure Code
			if(isset($vars['closureCode']) AND $this->closureCode != $vars['closureCode'] AND isset($closureCode))
			{
				$oldClosureCode = null;
				
				$oldClosureCode = new \Attribute();
				if(!$oldClosureCode->loadFromCode('srvc', 'tclc', $this->closureCode))
				{
					$oldClosureCode = new WorkspaceAttribute();
					$oldClosureCode->loadFromCode($workspace->getId(), 'tclc', $this->status);
				}
				
				$logString .= "Closure Code set to '" . ($closureCode->getName() !== NULL ? $closureCode->getName() : $wClosureCode->getName()) . "' from '" . $oldClosureCode->getName() . "'\n";
			}
			
			// Type
			if($this->type != $type->getId())
			{
				$oldType = new WorkspaceAttribute($this->type);
				$oldType->load();
				
				$logString .= "Type set to '" . $type->getName() . "' from '" . $oldType->getName() . "'\n";
			}
			
			if($this->category != $category->getId())
			{
				$oldCategory = new WorkspaceAttribute($this->category);
				$oldCategory->load();
				
				$logString .= "Category set to '" . $category->getName() . "' from '" . $oldCategory->getName() . "'\n";
			}
			
			// Severity
			if($this->severity != $severity->getId())
			{
				$oldSeverity = new WorkspaceAttribute($this->severity);
				$oldSeverity->load();
				
				$logString .= "Severity set to '" . $severity->getName() . "' from '" . $oldSeverity->getName() . "'\n";
			}
			
			// Priority
			if($this->priority != $vars['priority'])
				$logString .= "Priority set to '" . $vars['priority'] . "' from '" . $this->priority . "'\n";
			
			// Work Scale
			if($this->scale != $vars['workScale'])
				$logString .= "Work Scale set to '" . $vars['workScale'] . "' from '" . $this->scale . "'\n";
			
			// Desired Due Date
			if($this->desiredDueDate != $vars['desiredDueDate'])
				$logString .= "Desired Due Date set to '" . $vars['desiredDueDate'] . "' from '" . $this->desiredDueDate . "'\n";
			
			// Target Date
			if($this->targetDate != $vars['targetDate'])
				$logString .= "Target Date set to '" . $vars['targetDate'] . "' from '" . $this->targetDate . "'\n";
			
			// Work Scheduled Date
			if($this->workScheduleDate != $vars['workScheduleDate'])
				$logString .= "Work Scheduled Date set to '" . $vars['workScheduleDate'] . "' from '" . $this->workScheduleDate . "'\n";
			
			// Next Review Date
			if($this->nextReviewDate != $vars['nextReviewDate'])
				$logString .= "Next Review Date set to '" . $vars['nextReviewDate'] . "' from '" . $this->nextReviewDate . "'\n";
			
			// Source
			if($this->source != $source->getId())
			{
				$oldSource = new \Attribute($this->source);
				$oldSource->load();
				
				$logString .= "Source set to '" . $source->getName() . "' from '" . $oldSource->getName() . "'\n";
			}
			
			// Vendor Info
			if($this->vendorInfo != $vars['vendorInfo'])
				$logString .= "Vendor Info set to '" . $vars['vendorInfo'] . "' from '" . $this->vendorInfo . "'\n";
			
			// Location
			if($this->location != $vars['location'])
				$logString .= "Location set to '" . $vars['location'] . "' from '" . $this->location . "'\n";
			
			// Description
			if(isset($vars['description']) AND strlen($vars['description']) != 0)
				$logString .= "Appended Description\n";
			
			// Assignees
			
			$currentAssignees = $this->getAssignees();
			if($currentAssignees === FALSE)
				$currentAssignees = []; // New ticket, no assignees
			
			// Deleted assignees
			foreach($currentAssignees as $assignee)
			{
				if(!in_array($assignee, $vars['assignees']))
				{
					$parts = explode('-', $assignee);
					
					if(sizeof($parts) > 0) // Team is present
					{
						$team = new Team($parts[0]);
						if($team->load())
						{
							if(sizeof($parts) > 1) // User in team
							{
								$assignee = new \User($parts[1]);
								if($assignee->load())
								{
									$logString .= "Deleted Assignee: " . $assignee->getFirstName() . " " . $assignee->getLastName() . " ("  . $team->getName() . ")\n";
								}
							}
							else
							{
								$logString .= "Deleted Assignee: " . $team->getName() . "\n";
							}
						}
					}					
				}
			}
			
			// New assignees
			foreach($vars['assignees'] as $assignee)
			{
				if(!in_array($assignee, $currentAssignees))
				{
					$parts = explode('-', $assignee);
					
					if(sizeof($parts) > 0) // Team is present
					{
						$team = new Team($parts[0]);
						if($team->load())
						{
							if(sizeof($parts) > 1) // User in team
							{
								$assignee = new \User($parts[1]);
								if($assignee->load())
								{
									$logString .= "Added Assignee: " . $assignee->getFirstName() . " " . $assignee->getLastName() . " ("  . $team->getName() . ")\n";
								}
							}
							else
							{
								$logString .= "Added Assignee: " . $team->getName() . "\n";
							}
						}
					}					
				}
			}
			
			$log->setData($logString);
			
			/////
			// ASSIGN VARIABLES
			/////
			
			// Workspace
			$this->workspace = $workspace->getId();
			
			// Title
			$this->title = $vars['title'];
			
			// User I.D. (from User, if present)
			if($user->getId() !== NULL)
				$this->contact = $user->getId();
			
			// Status (straight)
			$this->status = $vars['status'];
			
			// Closure Code (straight)
			if(isset($vars['closureCode']))
				$this->closureCode = $vars['closureCode'];
			
			// Type (id)
			$this->type = $type->getId();
			
			// Category (id)
			$this->category = $category->getId();
			
			// Severity (id)
			$this->severity = $severity->getId();
			
			// Priority (straight)
			$this->priority = $vars['priority'];
			
			// Work Scale (straight)
			$this->scale = $vars['workScale'];
			
			// Desired Due Date (straight)
			$this->desiredDueDate = $vars['desiredDueDate'];
			
			// Target Date (straight)
			$this->targetDate = $vars['targetDate'];
			
			// Work Scheduled Date (straight)
			$this->workScheduleDate = $vars['workScheduleDate'];
			
			// Next Review Date (straight)
			$this->nextReviewDate = $vars['nextReviewDate'];
			
			// Source (id)
			$this->source = $source->getId();
			
			// Vendor Info (straight)
			$this->vendorInfo = $vars['vendorInfo'];
			
			// Location (straight)
			$this->location = $vars['location'];
			
			return $log;
		}
		
		/**
		* Send out a notification if the option(s) have been selected
		*/
		private function notify($vars = [])
		{
			$message = [];
			$message['title'] = "Updated: " . $this->title . " [Ticket=" . $this->number . " Workspace=" . $this->workspace . "]";
			
			if(isset($vars['notify-contact']) AND $this->contact !== NULL) // notify contact
			{
				$notification = new \Notification();
				$message['users'] = [$this->contact];
				$message['message'] = "Your support ticket #" . $this->number . " (" . 
					$this->title . ") has been updated with the following message:\n\n" . $vars['description'];
				$message['email'] = TRUE;
				$message['important'] = "no";
				$notification->send($message);
			}
			
			if(isset($vars['notify-assignees']))
			{
				$notification = new \Notification();
				$message['users'] = [];
				
				foreach($this->getAssignees() as $assigneeId)
				{
					$assignee = explode("-", $assigneeId);
					if(sizeof($assignee) > 1) // Does this assignment contain users?
					{
						if(!in_array($assignee[1], $message['users']))
							$message['users'][] = $assignee[1];
					}
					else // Is this assignment a team?
					{
						$team = new Team($assigneeId);
						if($team->load())
						{
							foreach($team->getMembers() as $member)
							{
								if(!in_array($member->getId(), $message['users']))
									$message['users'][] = $member->getId();
							}
						}
					}
				}
				
				$message['message'] = "Ticket #" . $this->number . " (" . $this->title . ") has been updated with the following message:\n\n" . $vars['description'];
				$message['email'] = TRUE;
				$mesage['important'] = "no";
				$notification->send($message);
			}
		}
		
		/**
		* Creates a new ticket from the Service Desk
		*/
		public function create($vars)
		{
			if(!isset($vars['assignees']) OR !is_array($vars['assignees']))
				$vars['assignees'] = [];
			
			// Validate
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			// Description required for initial ticket creation
			if(!isset($vars['description']) OR strlen($vars['description']) == 0)
				return ['Description Required'];
			
			// Create ticket
			global $conn;
			global $faCurrentUser;
			
			$this->number = getNextTicketNumber($this->workspace);
			
			$this->createUser = $faCurrentUser->getId();
			
			$conn->beginTransaction();
			
			if(!$this->post())
			{
				$conn->rollback();
				return FALSE;
			}
			
			if(strlen($val->getData()) != 0)
			{
				// Create log
				$val->setTicket($this->id);
				$val->setUser($faCurrentUser->getId());
				$val->setSeconds(null);
				$logString = "New Ticket\n" . $val->getData();
				$val->setData($logString);
				
				if(!$val->create())
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			// Create update
			$update = new TicketDetail();
			$update->setTicket($this->id);
			$update->setUser($faCurrentUser->getId());
			$update->setData($vars['description']);
			$update->setDetailType('u');
			$update->setSeconds(null);
			
			if(!$update->create())
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Add assignees
			if(isset($vars['assignees']))
			{					
				foreach($vars['assignees'] as $assignee)
				{
					$parts = explode('-', $assignee); // Split team from user (if present)
					
					if(sizeof($parts) > 0) // Team is present
					{
						$team = new Team($parts[0]);
						if($team->load())
						{
							if(sizeof($parts) > 1) // User is present
							{
								$member = new \User($parts[1]);
								if($member->load())
								{
									// Assign team/user
									$this->assign($team->getId(), $member->getId());
								}
							}
							else
							{
								// Assign team only
								$this->assign($team->getId());
							}
						}
					}
				}
			}
			
			$conn->commit();
			
			$this->notify($vars);
			
			return TRUE;
		}
		
		/**
		* Update ticket from the Service Desk
		*/
		public function save($vars)
		{
			if(!isset($vars['assignees']) OR !is_array($vars['assignees']))
				$vars['assignees'] = [];
			
			// Validate
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			global $conn;
			global $faCurrentUser;
			
			$conn->beginTransaction();
			
			if(!$this->put())
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create log
			if(strlen($val->getData()) != 0)
			{
				$val->setTicket($this->id);
				$val->setUser($faCurrentUser->getId());
				$val->setSeconds(null);
				
				if(!$val->create())
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			// Create update
			if(isset($vars['description']) AND strlen($vars['description']) != 0)
			{
				$update = new TicketDetail();
				$update->setTicket($this->id);
				$update->setUser($faCurrentUser->getId());
				$update->setData($vars['description']);
				$update->setDetailType('u');
				$update->setSeconds(null);
				
				if(!$update->create())
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			$currentAssignees = $this->getAssignees();
			
			// Deleted assignees
			foreach($currentAssignees as $assignee)
			{
				if(!in_array($assignee, $vars['assignees']))
				{
					$parts = explode('-', $assignee);
					
					if(sizeof($parts) > 0) // Team is present
					{
						$team = new Team($parts[0]);
						if($team->load())
						{
							if(sizeof($parts) > 1) // User in team
							{
								$user = new \User($parts[1]);
								if($user->load())
								{
									$this->deassign($team->getId(), $user->getId());
								}
							}
							else
							{
								$this->deassign($team->getId());
							}
						}
					}					
				}
			}
			
			// New assignees
			foreach($vars['assignees'] as $assignee)
			{
				if(!in_array($assignee, $currentAssignees))
				{
					$parts = explode('-', $assignee);
					
					if(sizeof($parts) > 0) // Team is present
					{
						$team = new Team($parts[0]);
						if($team->load())
						{
							if(sizeof($parts) > 1) // User in team
							{
								$user = new \User($parts[1]);
								if($user->load())
								{
									$this->assign($team->getId(), $user->getId());
								}
							}
							else
							{
								$this->assign($team->getId());
							}
						}
					}					
				}
			}
			
			$conn->commit();
			
			$this->notify($vars);
			
			return TRUE;
		}
		
		/**
		* Create a new ticket as a request from the customer portal
		* Sets status to new
		* Sets severity to the default for workspace
		* Set priority to 0
		* Set scale to 0
		* Set contact to current user
		*/
		public function createRequest($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			global $faCurrentUser;
			
			$errors = [];
			
			/////
			// VALIDATION
			/////
			
			$validator = new \Validator();
			
			// Title - is set, not greater than 64 chars
			if(isset($vars['title']) === FALSE OR !$validator->validLength($vars['title'], 1 ,64))
				$errors[] = "Title Must Be Between 1 And 64 Characters";
			
			// Request type - is set, is valid
			if(isset($vars['type']) === FALSE OR $vars['type'] == "")
				$errors[] = "Request Type Required";
			else
			{
				$type = new WorkspaceAttribute($vars['type']);
				if(!$type->load())
					$errors[] = "Request Type Is Invalid";
			}
			
			// Category - is set, is valid
			if(isset($vars['category']) === FALSE OR $vars['category'] == "")
				$errors[] = "Category Required";
			else
			{
				$category = new WorkspaceAttribute($vars['category']);
				if(!$category->load())
					$errors[] = "Category Is Invalid";
			}
			
			// Desired due date (optional) - is valid
			if(isset($vars['desiredDueDate']) === TRUE AND strlen($vars['desiredDueDate']) != 0 AND !$validator->validDate($vars['desiredDueDate']))
				$errors[] = "Desired Due Date Is Invalid";
			else if(isset($vars['desiredDueDate']) === FALSE OR strlen($vars['desiredDueDate']) == 0)
				$vars['desiredDueDate'] = NULL;
			
			// Location (optional)
			if(!isset($vars['location']))
				$vars['location'] = "";
			
			// Description - is set
			if(isset($vars['description']) === FALSE OR strlen($vars['description']) == 0)
				$errors[] = "Description Required";
			
			if(!empty($errors))
				return $errors;
			
			/////
			// PROCESS
			/////
			
			// Set validated attributes
			$this->title = $vars['title'];
			$this->category = $vars['category'];
			$this->type = $vars['type'];
			$this->desiredDueDate = $vars['desiredDueDate'];
			$this->location = $vars['location'];
			
			// Set status to 'New'
			$status = new \Attribute();
			if(!$status->loadFromCode('srvc', 'tsta', 'new'))
				return FALSE;
			$this->status = $status->getCode();
			
			// Set severity to default for workspace
			$workspace = new Workspace($this->workspace);
			$severity = $workspace->getDefaultAttribute('seve');
			if($severity === FALSE)
				throw new AppException("No Default Severity Has Been Assigned For This Workspace", "B02");
			
			$this->severity = $severity->getId();
			
			// Set source
			$source = new \Attribute();
			if(!$source->loadFromCode('srvc', 'tsrc', 'self'))
				return FALSE;
			$this->source = $source->getId();
			
			// Set priority, scale
			$this->priority = 0;
			$this->scale = 0;
			
			// Set contact
			$this->contact = $faCurrentUser->getId();
			
			// Set blank fields
			$this->vendorInfo = $this->deviceAsset = $this->deviceOS = $this->deviceMAC = $this->deviceDNSHostname = $this->deviceIP = $this->deviceLocation = $this->deviceSerial = $this->appName = $this->appNumber = "";
			
			/////
			// CREATE REQUEST
			/////
			
			global $conn;
			
			$conn->beginTransaction();
			
			if(!isset($this->workspace))
				return FALSE;
			
			$this->number = getNextTicketNumber($this->workspace);
			$this->createUser = $faCurrentUser->getId();			
			
			if(!$this->post())
			{
				$conn->rollback();
				return FALSE;
			}
			
			/////
			// CREATE REQUEST UPDATE
			/////
			
			$detail = new TicketDetail();
			$detail->setDetailType('u');
			$detail->setTicket($this->id);
			$detail->setData($vars['description']);
			$detail->setSeconds(0);
			$detail->setUser($faCurrentUser->getId());
			
			if($detail->create() !== TRUE)
			{
				$conn->rollback();
				return FALSE;
			}
			
			/////
			// CREATE REQUEST LOG
			/////
			$ticketLog = "New Customer Request
				Title: " . $vars['title'] . "
				User: " . $faCurrentUser->getFirstName() . " " . $faCurrentUser->getLastName() . " (" . $faCurrentUser->getUsername() . ")
				Type: " . $type->getName() . "
				Category: " . $category->getName() . "
				Severity: " . $severity->getName() . "
				Status: " . $status->getName() . "
				Desired Due Date: " . $vars['desiredDueDate'] . "
				Appended Description";
			
			$detail->setDetailType('l');
			$detail->setData($ticketLog);
			if($detail->create() !== TRUE)
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Append an update to this ticket from the Customer Portal
		* Creates a new ticket update and log entry
		* Sets ticket status to 'Customer Responded'
		*/
		public function updateRequest($vars = [])
		{
			if(empty($vars) or !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$errs = [];
			
			// Description
			if(!isset($vars['description']) OR strlen($vars['description']) == 0)
				$errs[] = "Description Required";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			global $conn;
			global $faCurrentUser;
			$conn->beginTransaction();
			
			// Set ticket status to 'Customer Responded'
			$status = new \Attribute();
			if(!$status->loadFromCode('srvc', 'tsta', 'cusr'))
				return FALSE;
			$this->status = $status->getCode();
			
			if(!$this->put())
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create ticket update
			
			$detail = new TicketDetail();
			$detail->setDetailType('u');
			$detail->setTicket($this->id);
			$detail->setData($vars['description']);
			$detail->setSeconds(0);
			$detail->setUser($faCurrentUser->getId());
			
			if($detail->create() !== TRUE)
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create ticket log
			$ticketLog = "Appended Description";
			
			$detail->setDetailType('l');
			$detail->setData($ticketLog);
			if($detail->create() !== TRUE)
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			
			// Send update to assignees
			$notification = new \Notification();
			$message = [];
			$message['title'] = "Contact Responded: " . $this->title . " [Ticket=" . $this->number . " Workspace=" . $this->workspace . "]";
			$message['users'] = [];
			
			foreach($this->getAssignees() as $assigneeId)
			{
				$assignee = explode("-", $assigneeId);
				if(sizeof($assignee) > 1) // Does this assignment contain users?
				{
					if(!in_array($assignee[1], $message['users']))
						$message['users'][] = $assignee[1];
				}
				else // Is this assignment a team?
				{
					$team = new Team($assigneeId);
					if($team->load())
					{
						foreach($team->getMembers() as $member)
						{
							if(!in_array($member->getId(), $message['users']))
								$message['users'][] = $member->getId();
						}
					}
				}
			}
			
			$message['message'] = "Ticket #" . $this->number . " (" . $this->title . ") has been updated by the contact with the following message:\n\n" . $vars['description'];
			$message['email'] = TRUE;
			$mesage['important'] = "no";
			$notification->send($message);
			
			return TRUE;
		}
	}
