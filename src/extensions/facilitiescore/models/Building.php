<?php namespace facilitiescore;

	/**
	* A unique facility, containing locations
	*/
	class Building
	{
		private $id;
		private $code;
		private $name;
		private $streetAddress;
		private $city;
		private $state;
		private $zipCode;
		private $createDate;
		private $createUser;
		private $lastModifyDate;
		private $lastModifyUser;
		private $picturePath;
		
		/**
		* Constructs a new Building
		* @param id The building id, if empty is ignored
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		///// GET-SET /////
		
		public function getId(){return $this->id;}
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		public function getStreetAddress(){return $this->streetAddress;}
		public function getCity(){return $this->city;}
		public function getState(){return $this->state;}
		public function getZipCode(){return $this->zipCode;}
		public function getCreateDate(){return $this->createDate;}
		public function getCreateUser(){return $this->createUser;}
		public function getLastModifyDate(){return $this->lastModifyDate;}
		public function getLastModifyUser(){return $this->lastModifyUser;}
		public function getPicturePath(){return $this->picturePath;}
		
		public function setId($id){$this->id = $id;}
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		public function setStreetAddress($streetAddress){$this->streetAddress = $streetAddress;}
		public function setCity($city){$this->city = $city;}
		public function setState($state){$this->state = $state;}
		public function setZipCode($zipCode){$this->zipCode = $zipCode;}
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
			
			$fetch = $conn->prepare("SELECT code, name, streetAddress, city, state, zipCode, createDate, lastModifyDate, lastModifyUser, picturePath, createUser FROM FacilitiesCore_Building WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$building = $fetch->fetch();
			
			$this->code = $building['code'];
			$this->name = $building['name'];
			$this->streetAddress = $building['streetAddress'];
			$this->city = $building['city'];
			$this->state = $building['state'];
			$this->zipCode = $building['zipCode'];
			$this->createDate = $building['createDate'];
			$this->lastModifyDate = $building['lastModifyDate'];
			$this->lastModifyUser = $building['lastModifyUser'];
			$this->picturePath = $building['picturePath'];
			$this->createUser = $building['createUser'];
			
			return TRUE;
		}
		
		/**
		* Fetches Building attributes from databases
		* Uses $code instead of $id
		* @return Was the fetch successful?
		*/
		private function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM FacilitiesCore_Building WHERE code = ? LIMIT 1");
			$fetch->bindParam(1, $this->code);
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
			
			$put = $conn->prepare("UPDATE FacilitiesCore_Building SET code = ?, name = ?, streetAddress = ?, city = ?, state = ?, zipCode = ?, createDate = ?, lastModifyDate = ?, lastModifyUser = ?, picturePath = ?, createUser = ? WHERE id = ?");
			$put->bindParam(1, $this->code);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->streetAddress);
			$put->bindParam(4, $this->city);
			$put->bindParam(5, $this->state);
			$put->bindParam(6, $this->zipCode);
			$put->bindParam(7, $this->createDate);
			$put->bindParam(8, $this->lastModifyDate);
			$put->bindParam(9, $this->lastModifyUser);
			$put->bindParam(10, $this->picturePath);
			$put->bindParam(11, $this->createUser);
			$put->bindParam(12, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Creates a new Building with current attributes
		* Updates this object's $id variable with the id of the new entry
		* @return Was the post successful?
		*/
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO FacilitiesCore_Building (code, name, streetAddress, city, state, zipCode, createDate, lastModifyDate, lastModifyUser, picturePath, createUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->code);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->streetAddress);
			$post->bindParam(4, $this->city);
			$post->bindParam(5, $this->state);
			$post->bindParam(6, $this->zipCode);
			$post->bindParam(7, $this->createDate);
			$post->bindParam(8, $this->lastModifyDate);
			$post->bindParam(9, $this->lastModifyUser);
			$post->bindParam(10, $this->picturePath);
			$post->bindParam(11, $this->createUser);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		/**
		* Drops the Building from the database
		* @return Was the drop successful?
		*/
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM FacilitiesCore_Building WHERE id = ?");
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
			
			// Code
			if((ifSet($vars['code']) === FALSE) OR !$validator->validLength($vars['code'], 1, 32))
				$errs[] = "Code Must Be Between 1 And 32 Characters";
			else if(!ctype_alnum($vars['code']))
				$errs[] = "Code Must Only Contain Letters And Numbers";
			else if($vars['code'] != $this->code)
			{
				$checkCode = new Building();
				
				if($checkCode->loadFromCode($vars['code']))
					$errs[] = "Code Already In Use";
			}
			
			// Name
			if((ifSet($vars['name']) == FALSE) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			
			// Street Address
			
			if((ifSet($vars['streetAddress']) == FALSE) OR strlen($vars['streetAddress']) == 0)
				$errs[] = "Street Address Required";
			
			// City
			
			if((ifSet($vars['city']) == FALSE) OR strlen($vars['city']) == 0)
				$errs[] = "City Required";
			
			// ZipCode
			if(!ctype_digit($vars['zipCode']) OR strlen($vars['zipCode']) != 5)
				$errs[] = "Zip Code Is Invalid";
			
			// State
			if(strlen($vars['state']) != 2)
				$errs[] = "State Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
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
		* Loads the Building attributes from the database using unique code
		* @param code The building's unique code.  If empty, attempts using the currently set code
		* @return Was the load successful?
		*/
		public function loadFromCode($code = FALSE)
		{
			if($code !== FALSE)
				$this->code = $code;
			
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
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			$this->streetAddress = $vars['streetAddress'];
			$this->city = $vars['city'];
			$this->state = $vars['state'];
			$this->zipCode = $vars['zipCode'];
			
			global $faCurrentUser;
			
			// Records current date and user
			$this->lastModifyUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			
			return $this->put();
		}
		
		/**
		* Creates a new Building with current attributes
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
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			$this->streetAddress = $vars['streetAddress'];
			$this->city = $vars['city'];
			$this->state = $vars['state'];
			$this->zipCode = $vars['zipCode'];
			
			global $faCurrentUser;
			
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->post();
		}
		
		/**
		* Marks the Building as deleted in the database
		* Does not remove the row from the table
		* @return Was the put successful?
		*/
		public function delete()
		{
			global $conn;
			
			$conn->beginTransaction();
			
			// Mark all locations as deleted
			foreach($this->getLocations() as $location)
			{
				if(!$location->delete())
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			if(!$this->drop())
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Returns a list of Locations for this building
		* @return Array of Location objects
		*/
		public function getLocations()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM FacilitiesCore_Location WHERE building = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$locations = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $locationId)
			{
				$location = new Location($locationId);
				if($location->load())
				{
					$locations[] = $location;
				}
			}
			
			return $locations;
		}
	}
