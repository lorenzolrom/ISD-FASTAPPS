<?php namespace itsmcore;
	/**
	* Generic ITSM item with a create date, deleted, delete date, last modify date, and last modify user
	*/
	class ITSMItem 
	{
		protected $id;
		protected $createDate;
		protected $lastModifyDate;
		protected $lastModifyUser;
		
		/// GET-SET
		
		public function getId(){return $this->id;}
		public function getCreateDate(){return $this->createDate;}
		public function getLastModifyDate(){return $this->lastModifyDate;}
		public function getLastModifyUser(){return $this->lastModifyUser;}
		
		public function setId($id){$this->id = $id;}
		public function setCreateDate($createDate){$this->createDate = $createDate;}
		public function setLastModifyDate($lastModifyDate){$this->lastModifyDate = $lastModifyDate;}
		public function setLastModifyUser($lastModifyUser){$this->lastModifyUser = $lastModifyUser;}
		
		/**
		* Constructor
		* @param $id Row ID, if left blank is ignored
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
	}
