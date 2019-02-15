<?php namespace itsmwebmanager;

	/**
	* Generic itsm webmanager item
	*/
	class ITSMItem
	{
		protected $id;
		protected $name;
		protected $createDate;
		protected $createUser;
		protected $lastModifyDate;
		protected $lastModifyUser;
		
		/**
		* Constructs new item.  Assigns numerical id if it is supplied.
		* @param $id Numerical row id
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
		public function getCreateDate(){return $this->createDate;}
		public function getCreateUser(){return $this->createUser;}
		public function getLastModifyDate(){return $this->lastModifyDate;}
		public function getLastModifyUser(){return $this->lastModifyUser;}
		
		public function setId($id){$this->id = $id;}
		public function setName($name){$this->name = $name;}
		public function setCreateDate($createDate){$this->createDate = $createDate;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		public function setLastModifyDate($lastModifyDate){$this->lastModifyDate = $lastModifyDate;}
		public function setLastModifyUser($lastModifyUser){$this->lastModifyUser = $lastModifyUser;}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		
		public function save($vars = [])
		{
			$u = $this->update($vars);
			if(is_array($u))
				return $u;
			if($u === FALSE)
				return FALSE;
			
			global $faCurrentUser;
			
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->put();
		}
		
		public function create($vars = [])
		{
			$u = $this->update($vars);
			if(is_array($u))
				return $u;
			if($u === FALSE)
				return FALSE;
			
			global $faCurrentUser;
			
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->post();
		}
		
		public function delete(){return $this->drop();}
	}
