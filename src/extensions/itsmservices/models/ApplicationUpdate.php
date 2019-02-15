<?php namespace itsmservices;
	/**
	* A status update to a service application
	*/
	class ApplicationUpdate
	{
		private $id;
		private $application;
		private $status;
		private $time;
		private $user;
		private $description;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function geId(){return $this->id;}
		public function getApplication(){return $this->application;}
		public function getStatus(){return $this->status;}
		public function getTime(){return $this->time;}
		public function getUser(){return $this->user;}
		public function getDescription(){return $this->description;}
		
		public function setId($id){$this->id = $id;}
		public function setApplication($application){$this->application = $application;}
		public function setStatus($status){$this->status = $status;}
		public function setDescription($description){$this->description = $description;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		public function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT application, status, time, user, description FROM 
				ITSM_ApplicationUpdate WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$update = $fetch->fetch();
			
			$this->application = $update['application'];
			$this->status = $update['status'];
			$this->time = $update['time'];
			$this->user = $update['user'];
			$this->description = $update['description'];
			
			return TRUE;
		}
		
		public function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_ApplicationUpdate (application, status, time, 
				user, description) VALUES (?, ?, NOW(), ?, ?)");
			$post->bindParam(1, $this->application);
			$post->bindParam(2, $this->status);
			$post->bindParam(3, $this->user);
			$post->bindParam(4, $this->description);
			$post->execute();
			
			if($post->rowCount() == 1)
			{
				$this->id = $conn->lastInsertId();
				return TRUE;
			}
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		private function validate($vars)
		{
			$validator = new \Validator();
			$errs = [];
			
			// Status - is set, is valid
			if(ifSet($vars['status']) === FALSE)
				$errs[] = "Status Required";
			else
			{
				$attr = new \Attribute($vars['status']);
				if(!$attr->load() OR !$validator->isValidAttribute('itsm', 'aits', $attr->getCode()))
					$errs[] = "Status Is Invalid";
			}
			
			// Description - is set
			if(ifSet($vars['description']) === FALSE OR strlen($vars['description']) == 0)
				$errs[] = "Description Is Required";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function load(){return $this->fetch();}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			if(!isset($this->application))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$app = new Application($this->application);
			if(!$app->load())
				return FALSE;
			
			if(!$app->changeStatus($vars['status']))
			{
				return FALSE;
			}
			
			$this->status = $vars['status'];
			$this->description = $vars['description'];
			$this->application = $app->getId();
			
			global $faCurrentUser;
			
			$this->user = $faCurrentUser->getId();
			
			if($this->post())
				return TRUE;
			
			return TRUE;
		}
	}
