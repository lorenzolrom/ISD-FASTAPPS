<?php namespace itsmservicemonitor;
	use itsmservices as itsmservices;

	/**
	* A category for applications on the monitor dashboard
	*/
	class ApplicationCategory
	{
		private $id;
		private $name;
		private $displayed;
		
		/**
		* Construct a new ApplicationCategory
		* @param $id Numerical ID of ApplicationCategory in database (optional)
		*/
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
		public function getDisplayed(){return $this->displayed;}
		
		public function setId($id){$this->id = $id;}
		public function setName($name){$this->name = $name;}
		public function setDisplayed($displayed){$this->displayed = $displayed;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT name, displayed FROM ITSM_ApplicationCategory WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1) // No results found
				return FALSE;
				
			$r = $f->fetch();
			$this->name = $r['name'];
			$this->displayed = $r['displayed'];
				
			return TRUE;
		}
		
		private function fetchFromName()
		{
			if(!isset($this->name))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ITSM_ApplicationCategory WHERE name = ? LIMIT 1");
			$f->bindParam(1, $this->name);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ITSM_ApplicationCategory (name, displayed) VALUES (?, ?)");
			$p->bindParam(1, $this->name);
			$p->bindParam(2, $this->displayed);
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId(); // Set this object's ID with the ID of the newly created row
			
			return TRUE;
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$p = $conn->prepare("UPDATE ITSM_ApplicationCategory SET name = ?, displayed = ? WHERE id = ?");
			$p->bindParam(1, $this->name);
			$p->bindParam(2, $this->displayed);
			$p->bindParam(3, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM ITSM_ApplicationCategory WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() !== 1)
				return FALSE;
			
			unset($this->id); // Remove this object's ID
			
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		/**
		* Validates supplied arguments and saves them as attributes
		* Or returns error messages
		*/
		private function update($vars = [])
		{
			/////
			// VALIDATION
			/////
			
			if(empty($vars) or !is_array($vars))
				return FALSE;
			
			$errs = [];
			
			// Name - is not greater than 64 chars, is unique
			if(!isset($vars['name']) OR (strlen($vars['name']) < 1 OR strlen($vars['name']) > 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			else if($vars['name'] != $this->name)
			{
				// Check for duplicate name
				$check = new ApplicationCategory();
				if($check->loadFromName($vars['name']))
					$errs[] = "Name Already In Use";
			}
			
			// Displayed - is 0 or 1
			if(!isset($vars['displayed']) OR !in_array($vars['displayed'], ['0', '1']))
				$errs[] = "Displayed Value Is Not Valid";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET ATTRIBUTES
			/////
			
			$this->name = $vars['name'];
			$this->displayed = $vars['displayed'];
			return TRUE;
		}
		
		/**
		* Mass update of applications from array of Application IDs
		*/
		private function updateApplications($apps)
		{
			$appIds = [];
			
			foreach($this->getApplications() as $app)
			{
				$appIds[] = $app->getId();
			}
			
			// Remove old apps
			foreach($appIds as $id)
			{
				if(!in_array($id, $apps))
					$this->removeApplication($id);
			}
			
			// Add new apps
			foreach($apps as $id)
			{
				if(!in_array($id, $appIds))
					$this->addApplication($id);
			}
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromName($name = FALSE)
		{
			if($name !== FALSE)
				$this->name = $name;
			
			return $this->fetchFromName();
		}
		
		public function create($vars)
		{
			$val = $this->update($vars);
			
			if(is_array($val))
				return $val;
			else if($val === FALSE)
				return $val;
			
			if(!$this->post())
				return FALSE;
			
			// Add new apps
			if(isset($vars['applications']) AND is_array($vars['applications']))
				$this->updateApplications($vars['applications']);
			
			return TRUE;
		}
		
		public function save($vars)
		{
			$val = $this->update($vars);
			
			if(is_array($val))
				return $val;
			else if($val === FALSE)
				return $val;
			
			if(isset($vars['applications']) AND is_array($vars['applications']))
				$this->updateApplications($vars['applications']);
			else
				$this->updateApplications([]);
			
			return $this->put();
		}
		
		public function delete()
		{
			return $this->drop();
		}
		
		public function getApplications()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT application FROM ITSM_Application_ApplicationCategory WHERE category = ?");
			$g->bindParam(1, $this->id);
			$g->execute();
			
			$apps = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
			{
				$app = new itsmservices\Application($id);
				if($app->load())
					$apps[] = $app;
			}
			
			return $apps;
		}
		
		public function addApplication($appId)
		{
			if(!isset($this->id))
				return FALSE;
			
			$app = new itsmservices\Application($appId);
			if(!$app->load())
				return ['Application Not Found'];
			
			global $conn;
			
			// Check if application is already assigned
			$c = $conn->prepare("SELECT application FROM ITSM_Application_ApplicationCategory WHERE application = ? AND category = ? LIMIT 1");
			$c->bindParam(1, $appId);
			$c->bindParam(2, $this->id);
			$c->execute();
			if($c->rowCount() == 1)
				return ['Application Is Already Assigned To This Category'];
			
			// Add app
			$a = $conn->prepare("INSERT INTO ITSM_Application_ApplicationCategory (application, category) VALUES (?, ?)");
			$a->bindParam(1, $appId);
			$a->bindParam(2, $this->id);
			$a->execute();
			
			if($a->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		public function removeApplication($appId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$r = $conn->prepare("DELETE FROM ITSM_Application_ApplicationCategory WHERE application = ? AND category = ?");
			$r->bindParam(1, $appId);
			$r->bindParam(2, $this->id);
			$r->execute();
			
			if($r->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
	}

