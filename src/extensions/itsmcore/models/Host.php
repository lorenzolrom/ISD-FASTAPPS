<?php namespace itsmcore;
	/**
	* A Device
	*/
	class Host extends ITSMItem
	{
		private $createUser;
		private $asset;
		private $ipAddress;
		private $macAddress;
		private $notes;
		private $systemName;
		private $systemCPU;
		private $systemRAM;
		private $systemOS;
		private $systemDomain;
		
		/////
		// GET-SET
		/////
		
		public function getCreateUser(){return $this->createUser;}
		public function getAsset(){return $this->asset;}
		public function getIpAddress(){return $this->ipAddress;}
		public function getMacAddress(){return $this->macAddress;}
		public function getNotes(){return $this->notes;}
		public function getSystemName(){return $this->systemName;}
		public function getSystemCPU(){return $this->systemCPU;}
		public function getSystemRAM(){return $this->systemRAM;}
		public function getSystemOS(){return $this->systemOS;}
		public function getSystemDomain(){return $this->systemDomain;}
		
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		public function setAsset($asset){$this->asset = $asset;}
		public function setIpAddress($ipAddress){$this->ipAddress = $ipAddress;}
		public function setMacAddress($macAddress){$this->macAddress = $macAddress;}
		public function setNotes($notes){$this->notes = $notes;}
		public function setSystemName($systemName){$this->systemName = $systemName;}
		public function setSystemCPU($systemCPU){$this->systemCPU = $systemCPU;}
		public function setSystemRAM($systemRAM){$this->systemRAM = $systemRAM;}
		public function setSystemOS($systemOS){$this->systemOS = $systemOS;}
		public function setSystemDomain($systemDomain){$this->systemDomain = $systemDomain;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT asset, ipAddress, macAddress, notes, systemName, 
				systemCPU, systemRAM, systemOS, systemDomain, createDate, createUser, 
				modifyDate, modifyUser FROM ITSM_Host WHERE id = ? LIMIT 1");
			
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$computer = $fetch->fetch();
			
			$this->asset = $computer['asset'];
			$this->ipAddress = $computer['ipAddress'];
			$this->macAddress = $computer['macAddress'];
			$this->notes = $computer['notes'];
			$this->systemName = $computer['systemName'];
			$this->systemCPU = $computer['systemCPU'];
			$this->systemRAM = $computer['systemRAM'];
			$this->systemOS = $computer['systemOS'];
			$this->systemDomain = $computer['systemDomain'];
			$this->createDate = $computer['createDate'];
			$this->createUser = $computer['createUser'];
			$this->lastModifyDate = $computer['modifyDate'];
			$this->lastModifyUser = $computer['modifyUser'];
			
			return TRUE;
		}
		
		private function fetchFromIP()
		{
			if(!isset($this->ipAddress))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Host WHERE ipAddress = ? LIMIT 1");
			$fetch->bindParam(1, $this->ipAddress);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			return $this->fetch();
		}
		
		private function fetchFromMAC()
		{
			if(!isset($this->macAddress))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Host WHERE macAddress = ? LIMIT 1");
			$fetch->bindParam(1, $this->ipAddress);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_Host (asset, ipAddress, macAddress, notes, 
				systemName, systemCPU, systemRAM, systemOS, systemDomain, createDate, createUser, 
				modifyDate, modifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->asset);
			$post->bindParam(2, $this->ipAddress);
			$post->bindParam(3, $this->macAddress);
			$post->bindParam(4, $this->notes);
			$post->bindParam(5, $this->systemName);
			$post->bindParam(6, $this->systemCPU);
			$post->bindParam(7, $this->systemRAM);
			$post->bindParam(8, $this->systemOS);
			$post->bindParam(9, $this->systemDomain);
			$post->bindParam(10, $this->createDate);
			$post->bindParam(11, $this->createUser);
			$post->bindParam(12, $this->lastModifyDate);
			$post->bindParam(13, $this->lastModifyUser);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE ITSM_Host SET asset = ?, ipAddress = ?, macAddress = ?, 
				notes = ?, systemName = ?, systemCPU = ?, systemRAM = ?, systemOS = ?, systemDomain = ?, 
				createDate = ?, createUser = ?, modifyDate = ?, modifyUser = ? WHERE id = ?");
				
			$put->bindParam(1, $this->asset);
			$put->bindParam(2, $this->ipAddress);
			$put->bindParam(3, $this->macAddress);
			$put->bindParam(4, $this->notes);
			$put->bindParam(5, $this->systemName);
			$put->bindParam(6, $this->systemCPU);
			$put->bindParam(7, $this->systemRAM);
			$put->bindParam(8, $this->systemOS);
			$put->bindParam(9, $this->systemDomain);
			$put->bindParam(10, $this->createDate);
			$put->bindParam(11, $this->createUser);
			$put->bindParam(12, $this->lastModifyDate);
			$put->bindParam(13, $this->lastModifyUser);
			$put->bindParam(14, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Host WHERE id = ?");
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
			$asset = new Asset();
			
			// Asset Tag
			if(ifSet($vars['assetTag']) === FALSE)
				$errs[] = "Asset Tag Required";
			else
			{
				if(!$asset->loadFromAssetTag($vars['assetTag']))
					$errs[] = "Asset Not Found";
			}
			
			// I.P. Address
			if((ifSet($vars['ipAddress']) === FALSE) OR !$validator->validLength($vars['ipAddress'], 1, 39))
				$errs[] = "IP Address Must Be Between 1 And 39 Characters";
			else if(!filter_var($vars['ipAddress'], FILTER_VALIDATE_IP))
				$errs[] = "IP Address Is Not Valid";
			else
			{
				if($this->ipAddress != $vars['ipAddress'])
				{
					$checkIP = new Host();
					if($checkIP->loadFromIP($vars['ipAddress']))
						$errs[] = "IP Address Already In Use";
				}
			}
			
			// MAC Address
			if(!ifSet($vars['macAddress']) OR strlen($vars['macAddress']) != 17)
				$errs[] = "MAC Address Must Be 17 Characters";
			else if (!preg_match("/^[A-Za-z0-9-]+$/", $vars['macAddress']))
				$errs[] = "MAC Address Must Only Contain Letters, Numbers, And '-'";
			else
			{
				if($this->macAddress != $vars['macAddress'])
				{
					$checkMAC = new Host();
					if($checkMAC->loadFromMAC($vars['macAddress']))
						$errs[] = "MAC Address Already In Use";
				}
			}
			
			// System Name
			if((ifSet($vars['systemName']) === FALSE) OR !$validator->validLength($vars['systemName'], 1, 64))
				$errs[] = "System Name Must Be Between 1 And 64 Characters";
			
			// System CPU (optional)
			if(!empty($vars['systemCPU']) AND !$validator->validLength($vars['systemCPU'], 0, 64))
				$errs[] = "System CPU Must Be Between 0 And 64 Characters";
			
			// System RAM (optional)
			if(!empty($vars['systemRAM']) AND !$validator->validLength($vars['systemRAM'], 0, 64))
				$errs[] = "System RAM Must Be Between 0 And 64 Characters";
			
			// System OS (optional)
			if(!empty($vars['systemOS']) AND !$validator->validLength($vars['systemOS'], 0, 64))
				$errs[] = "System OS Must Be Between 0 And 64 Characters";
			
			// System Domain (optional)
			if(!empty($vars['systemDomain']) AND !$validator->validLength($vars['systemDomain'], 0, 64))
				$errs[] = "System Domain Must Be Between 0 And 64 Characters";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromIP($ipAddress = FALSE)
		{
			if($ipAddress !== FALSE)
				$this->ipAddress = $ipAddress;
			
			return $this->fetchFromIP();
		}
		
		public function loadFromMac($macAddress = FALSE)
		{
			if($macAddress !== FALSE)
				$this->macAddress = $macAddress;
			
			return $this->fetchFromMAC();
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$asset = new Asset();
			$asset->loadFromAssetTag($vars['assetTag']);
			
			$this->asset = $asset->getId();
			$this->ipAddress = $vars['ipAddress'];
			$this->macAddress = $vars['macAddress'];
			$this->systemName = $vars['systemName'];
			$this->systemCPU = $vars['systemCPU'];
			$this->systemRAM = $vars['systemRAM'];
			$this->systemOS = $vars['systemOS'];
			$this->systemDomain = $vars['systemDomain'];
			
			global $faCurrentUser;
			
			$this->createUser = $faCurrentUser->getId();
			$this->createDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			
			return $this->post();
		}
		
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$asset = new Asset();
			$asset->loadFromAssetTag($vars['assetTag']);
			
			$this->asset = $asset->getId();
			$this->ipAddress = $vars['ipAddress'];
			$this->macAddress = $vars['macAddress'];
			$this->systemName = $vars['systemName'];
			$this->systemCPU = $vars['systemCPU'];
			$this->systemRAM = $vars['systemRAM'];
			$this->systemOS = $vars['systemOS'];
			$this->systemDomain = $vars['systemDomain'];
			
			global $faCurrentUser;
			
			$this->lastModifyUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			
			return $this->put();
		}
		
		public function delete()
		{
			return $this->drop();
		}
		
		public function isOnline()
		{
			return ping($this->ipAddress);
		}
	}
