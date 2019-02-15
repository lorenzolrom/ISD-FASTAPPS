<?php namespace servicenter;

	/**
	* Workspace for tickets
	*/
	class Workspace
	{
		private $id;
		private $name;
		private $default;
		private $priorityLevels;
		private $scaleLevels;
		private $widgetCount;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getName(){return $this->name;}
		public function getDefault(){return $this->default;}
		public function getPriorityLevels(){return $this->priorityLevels;}
		public function getScaleLevels(){return $this->scaleLevels;}
		public function getWidgetCount(){return $this->widgetCount;}
		
		public function setId($id){$this->id = $id;}
		public function setName($name){$this->name = $name;}
		public function setPriorityLevels($priorityLevels){$this->priorityLevels = $priorityLevels;}
		public function setScaleLevels($scaleLevels){$this->scaleLevels = $scaleLevels;}
		public function setWidgetCount($widgetCount){$this->widgetCount = $widgetCount;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT name, `default`, priorityLevels, scaleLevels, widgetCount FROM ServiCenter_Workspace WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$w = $f->fetch();
			
			$this->name = $w['name'];
			$this->default = $w['default'];
			$this->priorityLevels = $w['priorityLevels'];
			$this->scaleLevels = $w['scaleLevels'];
			$this->widgetCount = $w['widgetCount'];
			return TRUE;
		}
		
		private function fetchFromName()
		{
			if(!isset($this->name))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ServiCenter_Workspace WHERE name = ? LIMIT 1");
			$f->bindParam(1, $this->name);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			return $this->fetch();
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$p = $conn->prepare("UPDATE ServiCenter_Workspace SET name = ?, priorityLevels = ?, scaleLevels = ?, widgetCount = ? WHERE id = ?");
			$p->bindParam(1, $this->name);
			$p->bindParam(2, $this->priorityLevels);
			$p->bindParam(3, $this->scaleLevels);
			$p->bindParam(4, $this->widgetCount);
			$p->bindParam(5, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ServiCenter_Workspace (name, priorityLevels, scaleLevels, widgetCount) VALUES (?, ?, ?, ?)");
			$p->bindParam(1, $this->name);
			$p->bindParam(2, $this->priorityLevels);
			$p->bindParam(3, $this->scaleLevels);
			$p->bindParam(4, $this->widgetCount);
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			// CHECK FOR TICKETS
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM ServiCenter_Workspace WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() != 1)
				return FALSE;
			
			unset($this->id);			
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		
		public function loadFromName($name = FALSE)
		{
			if($name !== FALSE)
				$this->name = $name;
			
			return $this->fetchFromName();
		}
		
		/**
		* Validate form variables
		*/
		private function validate($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$validator = new \Validator();
			
			$errs = [];
			
			// Name - is set, is not greater than 64 characters, is not in use
			if(!isset($vars['name']) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			else
			{
				// Workspace is set and the submitted name is NOT the same as the current name
				if($vars['name'] != $this->name)
				{
					$c = new Workspace();
					if($c->loadFromName($vars['name']))
						$errs[] = "Name Already In Use";
				}
			}
			
			// Default - is set, is 0 or 1
			if(!isset($vars['default']) OR !in_array($vars['default'], [0, 1]))
				$errs[] = "Default Value Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function save($vars = [])
		{
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			if($vars['default'] == 1)
				$this->setDefault();
			$this->name = strip_tags(htmlentities($vars['name']));
			
			return $this->put();
		}
		
		public function create($vars = [])
		{
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			$this->name = strip_tags(htmlentities($vars['name']));
			$this->default = $vars['default'];
			$this->priorityLevels = 0;
			$this->scaleLevels = 0;
			$this->widgetCount = 3;
			
			if(!$this->post())
				return FALSE;
			
			global $conn;
			
			// Check for a default workspace
			$c = $conn->prepare("SELECT id FROM ServiCenter_Workspace WHERE `default` = 1 LIMIT 1");
			$c->execute();
			if($c->rowCount() != 1) // If there are no default workspace, set this one to default
				return $this->setDefault();
			
			return TRUE;
		}
		
		public function delete(){return $this->drop();}
		
		/**
		* Sets this workspace as the default
		*/
		public function setDefault()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$conn->beginTransaction();
			
			// Set all other workspace to not default
			$n = $conn->prepare("UPDATE ServiCenter_Workspace SET `default` = 0");
			$n->execute();
			
			// Set this workspace to default
			$d = $conn->prepare("UPDATE ServiCenter_Workspace SET `default` = 1 WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() == 1)
			{
				$conn->commit();
				return TRUE;
			}
			
			$conn->rollback();
			return FALSE;
		}
		
		/**
		* @return Array of Team objects with membership to this workspace
		*/
		public function getTeams()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT team FROM ServiCenter_Team_Workspace WHERE workspace = ?");
			$g->bindParam(1, $this->id);
			$g->execute();
			
			$teams = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$team = new Team($id);
				if($team->load())
					$teams[] = $team;
			}
			
			return $teams;
		}
		
		/**
		* Adds a team to this workspace
		* @param $teamId Numerical ID of the team
		*/
		public function addTeam($teamId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			// Check if team exists
			$t = new Team($teamId);
			if(!$t->load())
				return FALSE;
			
			// Check if a team is a member of this workspace
			$c = $conn->prepare("SELECT team FROM ServiCenter_Team_Workspace WHERE team = ? AND workspace = ? LIMIT 1");
			$c->bindParam(1, $teamId);
			$c->bindParam(2, $this->id);
			$c->execute();
			
			if($c->rowCount() == 1)
				return FALSE;
			
			// Add team
			$a = $conn->prepare("INSERT INTO ServiCenter_Team_Workspace (team, workspace) VALUES (?, ?)");
			$a->bindParam(1, $teamId);
			$a->bindParam(2, $this->id);
			$a->execute();
			
			if($a->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		public function removeTeam($teamId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$r = $conn->prepare("DELETE FROM ServiCenter_Team_Workspace WHERE team = ? AND workspace = ?");
			$r->bindParam(1, $teamId);
			$r->bindParam(2, $this->id);
			$r->execute();
			
			if($r->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Gets attributes for this workspace
		* @param $type 4-Character type code
		* @return Array of WorkspaceAttributes
		*/
		public function getAttributes($type)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WHERE workspace = ? AND type = ?");
			$g->bindParam(1, $this->id);
			$g->bindParam(2, $type);
			$g->execute();
			
			$as = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$a = new WorkspaceAttribute($id);
				if($a->load())
					$as[] = $a;
			}
			
			return $as;
		}
		
		/**
		* Gets the default attribute for a given type
		* @param $type 4-Character type code
		* @return WorkspaceAttribute object, or false if one not found
		*/
		public function getDefaultAttribute($type)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WHERE workspace = ? AND `default` = 1 AND type = ? LIMIT 1");
			$g->bindparam(1, $this->id);
			$g->bindparam(2, $type);
			$g->execute();
			
			if($g->rowCount() != 1)
				return FALSE;
			
			$a = new WorkspaceAttribute($g->fetchColumn());
			
			if($a->load())
				return $a;
			
			return FALSE;
		}
		
		public function changeScales($vars = [])
		{
			/////
			// VALIDATION
			/////
			
			if(empty($vars) or !is_array($vars))
				return FALSE;
			
			$errs = [];
			
			// Priority - is set, is digit only
			if(!isset($vars['priority']) OR !ctype_digit($vars['priority']))
				$errs[] = "Priority Is Invalid";
			
			// Work Scale - is set, is digit only
			if(!isset($vars['scale']) OR !ctype_digit($vars['scale']))
				$errs[] = "Work Scale Is Invalid";
			
			// Widget Count - is set, is digit only
			if(!isset($vars['widgetCount']) OR !ctype_digit($vars['widgetCount']))
				$errs[] = "Widget Count Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			$this->priorityLevels = $vars['priority'];
			$this->scaleLevels = $vars['scale'];
			$this->widgetCount = $vars['widgetCount'];
			
			return $this->put();
		}
	
		/**
		* Add a widget to this workspace
		* @param $position The position the widget should be in
		* @param $index The index of the widget in the array defined in config.php
		*/
		public function addWidget($position, $index)
		{
			global $conn;
			
			// Remove existing widget at position
			$this->removeWidget($position);
			
			// Add widget
			$a = $conn->prepare("INSERT INTO ServiCenter_Workspace_Widget (workspace, position, widget) VALUES (?, ?, ?)");
			$a->bindParam(1, $this->id);
			$a->bindParam(2, $position);
			$a->bindParam(3, $index);
			$a->execute();
			
			if($a->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove widget from the indicated position
		*/
		public function removeWidget($position)
		{
			global $conn;
			
			$r = $conn->prepare("DELETE FROM ServiCenter_Workspace_Widget WHERE workspace = ? AND position = ?");
			$r->bindParam(1, $this->id);
			$r->bindParam(2, $position);
			$r->execute();
			
			if($r->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
	
		/**
		* Returns an array of widget positions occupied, and the title of the widget that does
		*/
		public function getWidgets()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			global $SERVICENTER_WIDGETS;
			
			$g = $conn->prepare("SELECT position,widget FROM ServiCenter_Workspace_Widget WHERE workspace = ?");
			$g->bindParam(1, $this->id);
			$g->execute();
			
			$widgets = [];
			
			foreach($g->fetchAll() as $widgetData)
			{
				if(isset($SERVICENTER_WIDGETS[$widgetData['widget']]))
				{
					$widgets[] = [$widgetData['position'], $SERVICENTER_WIDGETS[$widgetData['widget']][0]];
				}
			}
			
			return $widgets;
		}
	
		/**
		* Returns the title and tickets for the widget at the indicated position
		*/
		public function getWidget($position)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $SERVICENTER_WIDGETS;
			global $SERVICENTER_WIDGET_LIMIT;
			global $conn;
			global $faCurrentUser;
			
			// Find widget, if it exists
			$f = $conn->prepare("SELECT widget FROM ServiCenter_Workspace_Widget WHERE workspace  = ? AND position = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->bindParam(2, $position);
			$f->execute();
			
			if($f->rowCount() != 1) // No widget in position
				return FALSE;
			
			$widgetIndex = $f->fetchColumn(); // Get widget's position in array
			
			// Check if there is a widget in that position
			if(!isset($SERVICENTER_WIDGETS[$widgetIndex]))
				return FALSE;
			
			// Build Widget
			$widget = $SERVICENTER_WIDGETS[$widgetIndex];
			
			$title = $widget[0]; // Get widget title
			
			// Query Tickets
			$params = $widget[1];
			$q = "SELECT ServiCenter_Ticket.id FROM ServiCenter_Ticket 
				INNER JOIN ServiCenter_TicketDetail ON ServiCenter_Ticket.id = ServiCenter_TicketDetail.ticket 
				WHERE ServiCenter_Ticket.workspace = ?";
			
			// Severity
			if(isset($params['seve']) AND is_array($params['seve']))
				$q .= " AND ServiCenter_Ticket.severity IN (SELECT ServiCenter_TicketAttribute.id FROM ServiCenter_TicketAttribute WHERE ServiCenter_TicketAttribute.type = 'seve' AND ServiCenter_TicketAttribute.code IN ('" . implode("','", $params['seve']) . "'))";
			
			// Type
			if(isset($params['type']) AND is_array($params['type']))
				$q .= " AND ServiCenter_Ticket.type IN (SELECT ServiCenter_TicketAttribute.id FROM ServiCenter_TicketAttribute WHERE ServiCenter_TicketAttribute.type = 'type' AND ServiCenter_TicketAttribute.code IN ('" . implode("','", $params['type']) . "'))";
			
			// Status
			if(isset($params['tsta']) AND is_array($params['tsta']))
				$q .= " AND ServiCenter_Ticket.status IN ('" . implode("','", $params['tsta']) . "')";
			
			// Not Status
			if(isset($params['nsta']) AND is_array($params['nsta']))
				$q .= " AND ServiCenter_Ticket.status NOT IN ('" . implode("','", $params['nsta']) . "')";
			
			// Assignee
			if(isset($params['assi']))
			{
				if($params['assi'] == 'self') // Get all tickets assigned to current user
				{
					$q .= " AND ServiCenter_Ticket.id IN (SELECT ServiCenter_Ticket_Assignee.ticket FROM ServiCenter_Ticket_Assignee WHERE ServiCenter_Ticket_Assignee.user = ?)";
				}
			}
			
			$q .= " GROUP BY ServiCenter_Ticket.id ORDER BY MAX(ServiCenter_TicketDetail.date) DESC";
			
			// Add limit to results
			if(isset($SERVICENTER_WIDGET_LIMIT) AND ctype_digit($SERVICENTER_WIDGET_LIMIT))
				$q .= " LIMIT " . $SERVICENTER_WIDGET_LIMIT;
			
			$g = $conn->prepare($q);
			$g->bindParam(1, $this->id);
			
			if(isset($params['assi']) AND $params['assi'] == "self")
				$g->bindParam(2, $faCurrentUser->getId());
			
			$g->execute();
			
			$tickets = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$ticket = new Ticket($id);
				if($ticket->load())
					$tickets[] = $ticket;
			}
			
			return [$title, $tickets];
		}
	}
