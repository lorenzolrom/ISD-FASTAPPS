<?php namespace servicenter;
	/**
	* A team of users
	*/
	class Team
	{
		private $id;
		private $name;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		////
		
		public function getId(){return $this->id;}
		public function getName(){return $this->name;}
		
		public function setId($id){$this->id = $id;}
		public function setName($name){$this->name = $name;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT name FROM ServiCenter_Team WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->name = $f->fetchColumn();
			
			return TRUE;
		}
		
		private function fetchFromName()
		{
			if(!isset($this->name))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ServiCenter_Team WHERE name = ? LIMIT 1");
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
			
			$p = $conn->prepare("UPDATE ServiCenter_Team SET name = ? WHERE id = ?");
			$p->bindParam(1, $this->name);
			$p->bindParam(2, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ServiCenter_Team (name) VALUES (?)");
			$p->bindParam(1, $this->name);
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return TRUE;
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM ServiCenter_Team WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() == 1)
				return TRUE;
			
			return FALSE;
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
		* Validate vars supplied for a save/create
		*/
		public function validate($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// Validation
			/////
			
			$validator = new \Validator();
			$errs = [];
			
			// Name - is set, is not greater than 64 characters, is not in use
			if(!isset($vars['name']) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			else
			{
				// Team is set and the submitted name is NOT the same as the current name
				if(!($vars['name'] == $this->name))
				{
					$c = new Team();
					if($c->loadFromName($vars['name']))
						$errs[] = "Name Already In Use";
				}
			}
			
			if(!empty($errs))
				return $errs;
			
			$this->name = $vars['name'];
			
			return TRUE;
		}
		
		public function save($vars = [])
		{
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			return $this->put();
		}
		
		public function create($vars = [])
		{
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			else
				return $this->post();
		}
		public function delete(){return $this->drop();}
		
		/**
		* @return Array of User objects that are memebers of this workspace
		*/
		public function getMembers()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT user FROM ServiCenter_Team_User WHERE team = ?");
			$g->bindParam(1, $this->id);
			$g->execute();
			
			$users = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$u = new \User($id);
				
				if($u->load())
					$users[] = $u;
			}
			
			return $users;
		}
		
		/**
		* Adds a user to this team
		* @param $vars Form variables
		*/
		public function addMember($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			/////
			// VALIDATION
			////
			
			$errs = [];
			
			if(!isset($vars['username']))
				$errs[] = "Username Required";
			else
			{
				$u = new \User();
				if(!$u->loadFromUsername($vars['username']))
					$errs[] = "Username Not Found";
			}
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			// Verify user is not already on team
			$v = $conn->prepare("SELECT team FROM ServiCenter_Team_User WHERE team = ? AND user = ? LIMIT 1");
			$v->bindParam(1, $this->id);
			$v->bindParam(2, $u->getId());
			$v->execute();
			
			if($v->rowCount() == 1)
				return FALSE;
			
			// Add user
			$a = $conn->prepare("INSERT INTO ServiCenter_Team_User (team, user) VALUES (?, ?)");
			$a->bindParam(1, $this->id);
			$a->bindParam(2, $u->getId());
			$a->execute();
			
			if($a->rowCount() != 1)
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* Removes a user from this team
		* @param $userId Numerical ID of the user to remove
		*/
		public function removeMember($userId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$r = $conn->prepare("DELETE FROM ServiCenter_Team_User WHERE team = ? AND user = ?");
			$r->bindParam(1, $this->id);
			$r->bindParam(2, $userId);
			$r->execute();
			
			if($r->rowCount() != 1)
				return FALSE;
			
			return TRUE;
		}
	}
