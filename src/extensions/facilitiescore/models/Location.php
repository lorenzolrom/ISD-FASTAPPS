<?php namespace facilitiescore;

	/**
	* A location within a building
	*/
	class Location
	{
		private $id;
		private $building;
		private $code;
		private $name;
		private $createDate;
		private $createUser;
		private $lastModifyDate;
		private $lastModifyUser;
		private $picturePath;
		
		/**
		* Constructs a new Location
		* @param id The building id, if empty is ignored
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		///// GET-SET /////
		
		public function getId(){return $this->id;}
		public function getBuilding(){return $this->building;}
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		public function getCreateDate(){return $this->createDate;}
		public function getCreateUser(){return $this->createUser;}
		public function getLastModifyDate(){return $this->lastModifyDate;}
		public function getLastModifyUser(){return $this->lastModifyUser;}
		public function getPicturePath(){return $this->picturePath;}
		
		public function setId($id){$this->id = $id;}
		public function setBuilding($building){$this->building = $building;}
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		public function setCreateDate($createDate){$this->createDate = $createDate;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		public function setLastModifyDate($lastModifyDate){$this->lastModifyDate = $lastModifyDate;}
		public function setLastModifyUser($lastModifyUser){$this->lastModifyUser = $lastModifyUser;}
		public function setPicturePath($picturePath){$this->picturePath = $picturePath;}
		
		///// DATABASE FUNCTIONS /////
		
		/**
		* Fetches Building attributes from database
		* @return Was the fetch successful?
		*/
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$fetch = $conn->prepare("SELECT building, code, name, createDate, lastModifyDate, lastModifyUser, picturePath, createUser FROM FacilitiesCore_Location WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$location = $fetch->fetch();
			
			$this->building = $location['building'];
			$this->code = $location['code'];
			$this->name = $location['name'];
			$this->createDate = $location['createDate'];
			$this->lastModifyDate = $location['lastModifyDate'];
			$this->lastModifyUser = $location['lastModifyUser'];
			$this->picturePath = $location['picturePath'];
			$this->createUser = $location['createUser'];
			
			return TRUE;
		}
		
		/**
		* Fetches Location attributes from databases
		* Uses $building and $code instead of $id
		* @return Was the fetch successful?
		*/
		private function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM FacilitiesCore_Location WHERE building = (SELECT id FROM FacilitiesCore_Building WHERE code = ?) AND code = ? LIMIT 1");
			$fetch->bindParam(1, $this->building);
			$fetch->bindParam(2, $this->code);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		/**
		* Updates database with current attributes
		* @return Was the put successful?
		*/
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE FacilitiesCore_Location SET building = ?, code = ?, name = ?, createDate = ?, lastModifyDate = ?, lastModifyUser = ?, picturePath = ?, createUser = ? WHERE id = ?");
			$put->bindParam(1, $this->building);
			$put->bindParam(2, $this->code);
			$put->bindParam(3, $this->name);
			$put->bindParam(4, $this->createDate);
			$put->bindParam(5, $this->lastModifyDate);
			$put->bindParam(6, $this->lastModifyUser);
			$put->bindParam(7, $this->picturePath);
			$put->bindParam(8, $this->createUser);
			$put->bindParam(9, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Creates a new Location with current attributes
		* Updates this object's $id variable with the id of the new entry
		* @return Was the post successful?
		*/
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO FacilitiesCore_Location (building, code, name, createDate, lastModifyDate, lastModifyUser, picturePath, createUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->building);
			$post->bindParam(2, $this->code);
			$post->bindParam(3, $this->name);
			$post->bindParam(4, $this->createDate);
			$post->bindParam(5, $this->lastModifyDate);
			$post->bindParam(6, $this->lastModifyUser);
			$post->bindParam(7, $this->picturePath);
			$post->bindParam(8, $this->createUser);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		/**
		* Drops the Location from the database
		* @return Was the drop successful?
		*/
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM FacilitiesCore_Location WHERE id = ?");
			$drop->bindParam(1, $this->id);
			$drop->execute();
			
			if($drop->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		///// BUSINESS FUNCTIONS /////
		
		private function validate($vars)
		{
			$validator = new \Validator();
			$errs = [];
			
			$building = new Building;
			if($building->loadFromCode($vars['buildingCode']))
			{
				// Code
				if(!ifSet($vars['code']) OR !$validator->validLength($vars['code'], 1, 32))
				{
					$errs[] = "Code Must Be Between 1 And 32 Characters";
				}
				else if(!ctype_alnum($vars['code']))
					$errs[] = "Code Must Only Contain Letters And Numbers";
				else
				{
					if($vars['code'] != $this->code)
					{
						// Does code exist for building?
						$checkCode = new Location();
						
						if($checkCode->loadFromCode($building->getId(), $vars['code']))
							$errs[] = "Code Already In Use";
					}
				}
			}
			else
			{
				$errs[] = "Building Code Is Invalid";
			}
			
			// Name
			if((ifSet($vars['name']) === FALSE) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			
			if(!empty($errs))
				return $errs;
		}
		
		/**
		* Loads the Building attributes from the database
		* @return Was the load successful?
		*/
		public function load()
		{
			return $this->fetch();
		}
		
		/**
		* Loads the Location attributes from the database using unique code
		* @param code The location's unique code.  If empty, attempts using the currently set code
		* @return Was the load successful?
		*/
		public function loadFromCode($building = FALSE, $code = FALSE)
		{
			if($code !== FALSE)
				$this->code = $code;
			
			if($building !== FALSE)
				$this->building = $building;
			
			return $this->fetchFromCode();
		}
		
		/**
		* Updates the database with current attributes
		* @return Was the put successful?
		*/
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$building = new Building();
			$building->loadFromCode($vars['buildingCode']);
			$this->building = $building->getId();
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			
			global $faCurrentUser;
			
			// Records current date and user
			$this->lastModifyUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			
			return $this->put();
		}
		
		/**
		* Creates a new Location with current attributes
		* Updates this object's $id with the id of the new entry
		* @return Was the post successful?
		*/
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$building = new Building();
			$building->loadFromCode($vars['buildingCode']);
			$this->building = $building->getId();
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			
			global $faCurrentUser;
			
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->post();
		}
		
		/**
		* Marks the Location as deleted in the database
		* Does not remove the row from the table
		* @return Was the put successful?
		*/
		public function delete()
		{			
			return $this->drop();
		}
	}
