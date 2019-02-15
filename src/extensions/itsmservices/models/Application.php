<?php namespace itsmservices;
	use itsmcore as itsmcore;
	use itsmwebmanager as itsmwebmanager;
	
	/**
	* A service application
	*/
	class Application
	{
		private $id;
		private $number;
		private $name;
		private $description;
		private $owner;
		private $type;
		private $publicFacing;
		private $lifeExpectancy;
		private $dataVolume;
		private $authType;
		private $port;
		private $createUser;
		private $createDate;
		private $lastModifyUser;
		private $lastModifyDate;
		private $status;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getNumber(){return $this->number;}
		public function getName(){return $this->name;}
		public function getDescription(){return $this->description;}
		public function getOwner(){return $this->owner;}
		public function getApplicationType(){return $this->type;}
		public function getPublicFacing(){return $this->publicFacing;}
		public function getLifeExpectancy(){return $this->lifeExpectancy;}
		public function getDataVolume(){return $this->dataVolume;}
		public function getAuthType(){return $this->authType;}
		public function getPort(){return $this->port;}
		public function getCreateUser(){return $this->createUser;}
		public function getLastModifyUser(){return $this->lastModifyUser;}
		public function getCreateDate(){return $this->createDate;}
		public function getLastModifyDate(){return $this->lastModifyDate;}
		public function getStatus(){return $this->status;}
		
		public function setId($id){$this->id = $id;}
		public function setNumber($number){$this->number = $number;}
		public function setName($name){$this->name = $name;}
		public function setDescription($description){$this->description = $description;}
		public function setOwner($owner){$this->owner = $owner;}
		public function setApplicationType($type){$this->type = $type;}
		public function setPublicFacing($publicFacing){$this->publicFacing = $publicFacing;}
		public function setLifeExpectancy($lifeExpectancy){$this->lifeExpectancy = $lifeExpectancy;}
		public function setDataVolume($dataVolume){$this->dataVolume = $dataVolume;}
		public function setAuthType($authType){$this->authType = $authType;}
		public function setPort($port){$this->port = $port;}
		public function setStatus($status){$this->status = $status;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT number, name, description, owner, type, publicFacing, 
				lifeExpectancy, dataVolume, authType, port, createUser, createDate, lastModifyDate, 
				lastModifyUser, status FROM ITSM_Application WHERE id = ? LIMIT 1");
			$fetch->bindparam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$application = $fetch->fetch();
			
			$this->number = $application['number'];
			$this->name = $application['name'];
			$this->description = $application['description'];
			$this->owner = $application['owner'];
			$this->type = $application['type'];
			$this->publicFacing = $application['publicFacing'];
			$this->lifeExpectancy = $application['lifeExpectancy'];
			$this->dataVolume = $application['dataVolume'];
			$this->authType = $application['authType'];
			$this->port = $application['port'];
			$this->createUser = $application['createUser'];
			$this->createDate = $application['createDate'];
			$this->lastModifyDate = $application['lastModifyDate'];
			$this->lastModifyUser = $application['lastModifyUser'];
			$this->status = $application['status'];
			
			return TRUE;
		}
		
		private function fetchFromNumber()
		{
			if(!isset($this->number))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Application WHERE number = ? LIMIT 1");
			$fetch->bindParam(1, $this->number);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE ITSM_Application SET number = ?, name = ?, description = ?, 
				owner = ?, type = ?, publicFacing = ?, lifeExpectancy = ?, dataVolume = ?, authType = ?, 
				port = ?, lastModifyDate = ?, lastModifyUser = ?, status = ? WHERE id = ?");
			
			$put->bindParam(1, $this->number);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->description);
			$put->bindParam(4, $this->owner);
			$put->bindParam(5, $this->type);
			$put->bindParam(6, $this->publicFacing);
			$put->bindParam(7, $this->lifeExpectancy);
			$put->bindParam(8, $this->dataVolume);
			$put->bindParam(9, $this->authType);
			$put->bindParam(10, $this->port);
			$put->bindParam(11, $this->lastModifyDate);
			$put->bindParam(12, $this->lastModifyUser);
			$put->bindParam(13, $this->status);
			$put->bindParam(14, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_Application (number, name, description, owner, type, 
				publicFacing, lifeExpectancy, dataVolume, authType, port, createUser, createDate, 
				lastModifyUser, lastModifyDate, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->number);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->description);
			$post->bindParam(4, $this->owner);
			$post->bindParam(5, $this->type);
			$post->bindParam(6, $this->publicFacing);
			$post->bindParam(7, $this->lifeExpectancy);
			$post->bindParam(8, $this->dataVolume);
			$post->bindParam(9, $this->authType);
			$post->bindParam(10, $this->port);
			$post->bindParam(11, $this->createUser);
			$post->bindParam(12, $this->createDate);
			$post->bindParam(13, $this->lastModifyUser);
			$post->bindParam(14, $this->lastModifyDate);
			$post->bindParam(15, $this->status);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Application WHERE id = ?");
			$drop->bindParam(1, $this->id);
			$drop->execute();
			
			if($drop->rowCount() != 1)
				return FALSE;
			
			unset($this->id);
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		private function validate($vars)
		{
			$validator = new \Validator();
			$errs = [];
			
			if(!isset($vars['appHosts']) OR !is_array($vars['appHosts']))
				$vars['appHosts'] = [];
			if(!isset($vars['webHosts']) OR !is_array($vars['webHosts']))
				$vars['webHosts'] = [];
			if(!isset($vars['vhosts']) OR !is_array($vars['vhosts']))
				$vars['vhosts'] = [];
			if(!isset($vars['dataHosts']) OR !is_array($vars['dataHosts']))
				$vars['dataHosts'] = [];
			
			$owner = NULL;
			
			// Name - is set, not greater than 64 chars
			if((ifSet($vars['name']) === FALSE) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			
			// Owner Username - is set, is valid
			if(ifSet($vars['ownerUsername']) === FALSE)
				$errs[] = "Owner Username Required";
			else
			{
				$owner = new \User();
				if(!$owner->loadFromUsername($vars['ownerUsername']))
					$errs[] = "Owner Username Not Found";
			}
			
			// App Type - is set, is valid
			if(ifSet($vars['applicationType']) === FALSE)
				$errs[] = "Application Type Required";
			else
			{
				$attr = new \Attribute($vars['applicationType']);
				if(!$attr->load() OR !$validator->isValidAttribute('itsm', 'aitt', $attr->getCode()))
					$errs[] = "Application Type Is Invalid";
			}
			
			// Life Expect - is set, is valid
			if(ifSet($vars['lifeExpectancy']) === FALSE)
				$errs[] = "Life Expectancy Required";
			else
			{
				$attr = new \Attribute($vars['lifeExpectancy']);
				if(!$attr->load() OR !$validator->isValidAttribute('itsm', 'aitl', $attr->getCode()))
					$errs[] = "Life Expectancy Is Invalid";
			}
			
			// Auth Type - is set, is valid
			if(ifSet($vars['authType']) === FALSE)
				$errs[] = "Authentication Type Required";
			else
			{
				$attr = new \Attribute($vars['authType']);
				if(!$attr->load() OR !$validator->isValidAttribute('itsm', 'aita', $attr->getCode()))
					$errs[] = "Authentication Type Is Invalid";
			}
			
			// Public Facing - is set, is valid
			if((ifSet($vars['publicFacing']) === FALSE) OR ($vars['publicFacing'] != 1 AND $vars['publicFacing'] != 0))
				$errs[] = "Public Facing Value Is Invalid";
			
			// Port (optional) - not greater than 5 chars
			if(isset($vars['port']) AND strlen($vars['port']) > 5)
				$errs[] = "Port Must Be Between 0 And 5 Characters";
			else if(!isset($vars['port']))
				$vars['port'] = "";
			
			// Data Vol - is set, is valid
			if(ifSet($vars['dataVolume']) === FALSE)
				$errs[] = "Data Volume Required";
			else
			{
				$attr = new \Attribute($vars['dataVolume']);
				if(!$attr->load() OR !$validator->isValidAttribute('itsm', 'aitd', $attr->getCode()))
					$errs[] = "Data Volume Is Invalid";
			}
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function load(){return $this->fetch();}
		
		public function loadFromNumber($number = FALSE)
		{
			if($number !== FALSE)
				$this->number = $number;
			
			return $this->fetchFromNumber();
		}
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			
			$owner = new \User();
			$owner->loadFromUsername($vars['ownerUsername']);
			
			$this->name = $vars['name'];
			$this->owner = $owner->getId();
			$this->type = $vars['applicationType'];
			$this->lifeExpectancy = $vars['lifeExpectancy'];
			$this->authType = $vars['authType'];
			$this->description = $vars['description'];
			$this->publicFacing = $vars['publicFacing'];
			$this->port = $vars['port'];
			$this->dataVolume = $vars['dataVolume'];
			
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			global $conn;
			$conn->beginTransaction();
			
			if(!$this->put())
			{
				$conn->rollback();
				return FALSE;
			}
			
			$this->removeAllHosts();
			$this->removeAllVHosts();
			
			if(empty($vars['appHosts']) OR !is_array($vars['appHosts']))
				$vars['appHosts'] = [];
			if(empty($vars['webHosts']) OR !is_array($vars['webHosts']))
				$vars['webHosts'] = [];
			if(empty($vars['dataHosts']) OR !is_array($vars['dataHosts']))
				$vars['dataHosts'] = [];
			if(empty($vars['vhosts']) OR !is_array($vars['vhosts']))
				$vars['vhosts'] = [];
			
			if(!$this->addHosts($vars['appHosts'], $vars['webHosts'], $vars['dataHosts'], $vars['vhosts']))
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			
			$owner = new \User();
			$owner->loadFromUsername($vars['ownerUsername']);
			
			$this->name = $vars['name'];
			$this->owner = $owner->getId();
			$this->type = $vars['applicationType'];
			$this->lifeExpectancy = $vars['lifeExpectancy'];
			$this->authType = $vars['authType'];
			$this->description = $vars['description'];
			$this->publicFacing = $vars['publicFacing'];
			$this->port = $vars['port'];
			$this->dataVolume = $vars['dataVolume'];
			
			$status = new \Attribute();
			if(!$status->loadFromCode('itsm', 'aits', 'addd'))
				throw new AppException("Failed To Set Application Status", "D09");
				
			$this->status = $status->getId();
			$this->number = getNextApplicationNumber();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			
			global $conn;
			$conn->beginTransaction();
			
			if(!$this->post())
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Create new AppUpdate
			$au = new ApplicationUpdate();
			$au->setApplication($this->id);
			
			$status = new \Attribute();
			if(!$status->loadFromCode('itsm', 'aits', 'addd'))
			{
				$conn->rollback();
				throw new AppException("Failed To Set Application Status", "D09");
			}
			
			$createAu = $au->create(['status' => $status->getId(), 'description' => "Application Added"]);
			
			if(is_array($createAu))
			{
				$conn->rollback();
				return $createAu;
			}
			else if($createAu !== TRUE)
			{
				$conn->rollback();
				return FALSE;
			}
			
			if(empty($vars['appHosts']) OR !is_array($vars['appHosts']))
				$vars['appHosts'] = [];
			if(empty($vars['webHosts']) OR !is_array($vars['webHosts']))
				$vars['webHosts'] = [];
			if(empty($vars['dataHosts']) OR !is_array($vars['dataHosts']))
				$vars['dataHosts'] = [];
			if(empty($vars['vhosts']) OR !is_array($vars['vhosts']))
				$vars['vhosts'] = [];
			
			if(!$this->addHosts($vars['appHosts'], $vars['webHosts'], $vars['dataHosts'], $vars['vhosts']))
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Bulk add all host types
		*/
		public function addHosts($appHosts = [], $webHosts = [], $dataHosts = [], $vhosts = [])
		{			
			foreach($appHosts as $id)
			{
				$host = new itsmcore\Host($id);
				if($host->load())
				{
					if(!$this->addHost('apph', $id))
						return FALSE;
				}
			}
			
			foreach($webHosts as $id)
			{
				$host = new itsmcore\Host($id);
				if($host->load())
				{
					if(!$this->addHost('webh', $id))
						return FALSE;
				}
			}
			
			foreach($dataHosts as $id)
			{
				$host = new itsmcore\Host($id);
				if($host->load())
				{
					if(!$this->addHost('data', $id))
						return FALSE;
				}
			}
			
			foreach($vhosts as $id)
			{
				$host = new itsmwebmanager\VHost($id);
				if($host->load())
				{
					if(!$this->addVHost($id))
						return FALSE;
				}
			}
			
			return TRUE;
		}
		
		/**
		* Add a host to this application of the specified type
		* @param $hostType 4-character host type
		* @param $hostId Numerical Host ID
		*/
		public function addHost($hostType, $hostId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$check = $conn->prepare("SELECT host FROM ITSM_Application_Host WHERE application = ? AND 
				host = ? AND relationship = ? LIMIT 1");
			$check->bindParam(1, $this->id);
			$check->bindParam(2, $hostId);
			$check->bindParam(3, $hostType);
			$check->execute();
			
			if($check->rowCount() == 1)
				return FALSE;
			
			$add = $conn->prepare("INSERT INTO ITSM_Application_Host (application, host, relationship) 
				VALUES (?, ?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $hostId);
			$add->bindParam(3, $hostType);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove a host from this application with the specified type
		* @param $hostType 4-character host type
		* @param $hostId Numerical Host ID
		*/
		public function removeHost($hostType, $hostId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$remove = $conn->prepare("REMOVE FROM ITSM_Application_Host WHERE application = ? 
				AND host = ? AND relationship = ?");
			$remove->bindParam(1, $this->id);
			$remove->bindParam(2, $hostId);
			$remove->bindParam(3, $hostType);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Add a virtual host to this application
		* @param $vhostId Numeric VHost ID
		*/
		public function addVHost($vhostId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$check = $conn->prepare("SELECT vhost FROM ITSM_Application_VHost WHERE application = ? AND 
				vhost = ? LIMIT 1");
			$check->bindParam(1, $this->id);
			$check->bindParam(2, $vhostId);
			$check->execute();
			
			if($check->rowCount() == 1)
				return FALSE;
			
			$add = $conn->prepare("INSERT INTO ITSM_Application_VHost (application, vhost) 
				VALUES (?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindparam(2, $vhostId);
			$add->execute();
			
			if($add->rowCount() != 1)
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* Removes a virtual host from this application
		* @param $vhostId Numeric VHost ID
		*/
		public function removeVHost($vhostId)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_Application_VHost WHERE application = ? AND 
				vhost = ?");
			$remove->bindParam(1, $this->id);
			$remove->bindParam(2, $vhostId);
			$remove->execute();
			
			if($remove->rowCount() != 1)
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* Get a list of all updates for this Application, ordered newest first
		* @return Array of ApplicationUpdate objects
		*/
		public function getUpdates()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_ApplicationUpdate WHERE application = ? ORDER BY 
				time DESC");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$updates = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $updateId)
			{
				$update = new ApplicationUpdate($updateId);
				
				if($update->load())
					$updates[] = $update;
			}
			
			return $updates;
		}
		
		public function getLastUpdate()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_ApplicationUpdate WHERE application = ? ORDER BY time DESC LIMIT 1");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			if($get->rowCount() != 1)
				return FALSE;
			
			$id = $get->fetchColumn();
			
			$update = new ApplicationUpdate($id);
			$update->load();
			
			return $update;
		}
		
		/**
		* Returns all hosts of the given type
		* @return Array of Host objects
		*/
		public function getHosts($hostType)
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$get = $conn->prepare("SELECT host FROM ITSM_Application_Host WHERE application = ? AND relationship = ?");
			$get->bindParam(1, $this->id);
			$get->bindParam(2, $hostType);
			$get->execute();
			
			$hosts = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $hostId)
			{
				$host = new itsmcore\Host($hostId);
				
				if($host->load())
					$hosts[] = $host;
			}
			
			return $hosts;
		}
		
		/**
		* Returns all VHosts 
		* @return Array of VHost objects
		*/
		public function getVHosts()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$get = $conn->prepare("SELECT vhost FROM ITSM_Application_VHost WHERE application = ?");
			$get->bindparam(1, $this->id);
			$get->execute();
			
			$vhosts = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $vhostId)
			{
				$vhost = new itsmwebmanager\VHost($vhostId);
				
				if($vhost->load())
					$vhosts[] = $vhost;
			}
			
			return $vhosts;
		}
		
		public function removeAllHosts()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_Application_Host WHERE application = ?");
			$remove->bindParam(1, $this->id);
			
			if($remove->execute())
				return TRUE;
			
			return FALSE;
		}
		
		public function removeAllVHosts()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_Application_VHost WHERE application = ?");
			$remove->bindParam(1, $this->id);
			
			if($remove->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Sets this applications status and immediately updates it in the database
		*/
		public function changeStatus($status)
		{
			if(!isset($this->id) OR !isset($status) OR !ctype_digit($status))
				return FALSE;
			
			$this->status = $status;
			return $this->put();
		}
	}
