<?php
	class Bulletin
	{
		private $id;
		private $user;
		private $startDate;
		private $endDate;
		private $title;
		private $message;
		private $inactive;
		private $type;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getUser(){return $this->user;}
		public function getStartDate(){return $this->startDate;}
		public function getEndDate()
		{
			if($this->endDate == "9999-12-31")
				return "";
			
			return $this->endDate;
		}
		
		public function getTitle(){return $this->title;}
		public function getMessage(){return $this->message;}
		public function getInactive(){return $this->inactive;}
		public function getBulletinType(){return $this->type;}
		
		/////
		// DATABASE
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			$f = $conn->prepare("SELECT user, startDate, endDate, title, message, inactive, `type` 
				FROM Bulletin WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$b = $f->fetch();
			
			$this->user = $b['user'];
			$this->startDate = $b['startDate'];
			$this->endDate = $b['endDate'];
			$this->title = $b['title'];
			$this->message = $b['message'];
			$this->inactive = $b['inactive'];
			$this->type = $b['type'];
			
			return TRUE;
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO Bulletin (user, startDate, endDate, title, 
				message, inactive, `type`) VALUES (?, ?, ?, ?, ?, ?, ?)");
			
			$p->bindParam(1, $this->user);
			$p->bindParam(2, $this->startDate);
			$p->bindParam(3, $this->endDate);
			$p->bindParam(4, $this->title);
			$p->bindParam(5, $this->message);
			$p->bindParam(6, $this->inactive);
			$p->bindParam(7, $this->type);
			
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
			
			$p = $conn->prepare("UPDATE Bulletin SET user = ?, startDate = ?, endDate = ?, 
				title = ?, message = ?, inactive = ?, `type` = ? WHERE id = ?");
			
			$p->bindParam(1, $this->user);
			$p->bindParam(2, $this->startDate);
			$p->bindParam(3, $this->endDate);
			$p->bindParam(4, $this->title);
			$p->bindParam(5, $this->message);
			$p->bindParam(6, $this->inactive);
			$p->bindParam(7, $this->type);
			$p->bindParam(8, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS
		/////
		
		/**
		* Validate input fields
		*/
		private function validate($vars = [])
		{
			if(!is_array($vars) OR empty($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new Validator();
			$errs = [];
			
			// Title - is set
			if(!isset($vars['title']) OR strlen($vars['title']) == 0)
				$errs[] = "Title Required";
			
			// Message - is set
			if(!isset($vars['message']) OR strlen($vars['message']) == 0)
				$errs[] = "Message Required";
			
			// Inactive - is 0 or 1
			if(!isset($vars['inactive']) OR ($vars['inactive'] != 1 AND $vars['inactive'] != 0))
				$errs[] = "Inactive Value Is Invalid";
			
			// Start date - is valid
			if(!isset($vars['startDate']))
				$errs[] = "Start Date Required";
			else if(!$validator->validDate($vars['startDate']))
				$errs[] = "Start Date Is Invalid";
			
			// End date (optional) - is valid, if not set make it 9999-12-31
			if(!isset($vars['endDate']))
				$errs[] = "End Date Required";
			else if(strlen($vars['endDate']) == 0)
				$vars['endDate'] = '9999-12-31';
			else if(!$validator->validDate($vars['endDate']))
				$errs[] = "End Date Is Invalid";
			
			// Type - is i or a
			if(!isset($vars['type']) OR ($vars['type'] != 'i' AND $vars['type'] != 'a'))
				$errs[] = "Type Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// UPDATE
			/////
			
			$this->title = $vars['title'];
			$this->message = $vars['message'];
			$this->inactive = $vars['inactive'];
			$this->startDate = $vars['startDate'];
			$this->endDate = $vars['endDate'];
			$this->type = $vars['type'];
			
		}
		
		public function load()
		{
			$fetch = $this->fetch();
			
			if(!$fetch)
				return FALSE;
			
			// check if message in inactive (end date passed)
			if($this->endDate < date('Y-m-d'))
			{
				$this->inactive = 1;
				return $this->put();
			}
			
			return TRUE;
		}
		
		public function create($vars)
		{
			global $conn;
			
			$valid = $this->validate($vars);
			
			if(is_array($valid))
				return $valid;
			
			$conn->beginTransaction();
			
			if($this->post() === FALSE)
			{
				$conn->rollBack();
				return FALSE;
			}
			
			if(!isset($vars['roles']) OR !is_array($vars['roles']))
				$vars['roles'] = [];
			
			foreach($vars['roles'] as $id)
			{
				if(!$this->addRole($id))
				{
					$conn->rollBack();
					return FALSE;
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		public function save($vars)
		{
			$valid = $this->validate($vars);
			
			if(is_array($valid))
				return $valid;
			
			global $conn;
			$conn->beginTransaction();
			
			if($this->put() === FALSE)
			{
				$conn->rollBack();
				return FALSE;
			}
			
			if(!isset($vars['roles']) OR !is_array($vars['roles']))
				$vars['roles'] = [];
			
			// Get current role IDs
			$roleIds = $this->getRoles();
			
			// Delete old roles
			foreach($roleIds as $id)
			{
				if(!in_array($id, $vars['roles']))
				{
					if(!$this->removeRole($id))
					{
						$conn->rollBack();
						return FALSE;
					}
				}
			}
			
			// Add new roles
			foreach($vars['roles'] as $id)
			{
				if(!in_array($id, $roleIds))
				{
					if(!$this->addRole($id))
					{
						$conn->rollBack();
						return FALSE;
					}
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Allow a role to see this bulletin
		* @param roleId numerical id of role
		* @return TRUE is role added, FALSE if role could not be added
		*/
		public function addRole($roleId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$role = new Role($roleId);
			if(!$role->load())
				return FALSE;
			
			$a = $conn->prepare("INSERT INTO Role_Bulletin (role, bulletin) VALUES 
				(?, ?)");
			
			$a->bindParam(1, $roleId);
			$a->bindParam(2, $this->id);
			$a->execute();
			
			if($a->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove a role from being able to see the bulletin
		* @param roleId numerical id of role to remove
		*/
		public function removeRole($roleId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$a = $conn->prepare("DELETE FROM Role_Bulletin WHERE role = ? AND bulletin = ?");
			$a->bindParam(1, $roleId);
			$a->bindParam(2, $this->id);
			$a->execute();
			
			if($a->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Return list of role IDs allowed to see this bulletin
		*/
		public function getRoles()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT role FROM Role_Bulletin WHERE bulletin = ?");
			$g->bindParam(1, $this->id);
			$g->execute();
			
			$roles = [];
			
			foreach($g->fetchAll(PDO::FETCH_COLUMN, 0) as $roleId)
			{
				$roles[] = $roleId;
			}
			
			return $roles;
		}
	}