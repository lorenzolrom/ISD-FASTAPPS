<?php namespace itsmwebmanager;
	use itsmcore;
	/**
	* An internet domain name
	*/
	class VHost extends ITSMItem
	{
		private $domain;
		private $subdomain;
		private $host;
		private $registrar;
		private $status;
		private $renewCost;
		private $notes;
		private $registerDate;
		private $expireDate;
		
		/////
		// GET-SET
		/////
		
		public function getDomain(){return $this->domain;}
		public function getSubdomain(){return $this->subdomain;}
		public function getHost(){return $this->host;}
		public function getRegistrar(){return $this->registrar;}
		public function getStatus(){return $this->status;}
		public function getRenewCost(){return $this->renewCost;}
		public function getNotes(){return $this->notes;}
		public function getRegisterDate(){return $this->registerDate;}
		public function getExpireDate(){return $this->expireDate;}
		
		public function setDomain($domain){$this->domain = $domain;}
		public function setSubdomain($subdomain){$this->subdomain = $subdomain;}
		public function setHost($host){$this->host = $host;}
		public function setRegistrar($registrar){$this->registrar = $registrar;}
		public function setStatus($status){$this->status = $status;}
		public function setRenewCost($renewCost){$this->renewCost = $renewCost;}
		public function setNotes($notes){$this->notes = $notes;}
		public function setRegisterDate($registerDate){$this->registerDate = $registerDate;}
		public function setExpireDate($expireDate){$this->expireDate = $expireDate;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		protected function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT domain, subdomain, name, host, registrar, status, renewCost, 
				notes, registerDate, expireDate, createDate, createUser, modifyDate, modifyUser 
				FROM ITSM_VHost WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$domain = $fetch->fetch();
			
			$this->domain = $domain['domain'];
			$this->subdomain = $domain['subdomain'];
			$this->name = $domain['name'];
			$this->host = $domain['host'];
			$this->registrar = $domain['registrar'];
			$this->status = $domain['status'];
			$this->renewCost = $domain['renewCost'];
			$this->notes = $domain['notes'];
			$this->registerDate = $domain['registerDate'];
			$this->expireDate = $domain['expireDate'];
			$this->createDate = $domain['createDate'];
			$this->createUser = $domain['createUser'];
			$this->lastModifyDate = $domain['modifyDate'];
			$this->lastModifyUser = $domain['modifyUser'];
			
			return TRUE;
		}
		
		protected function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE ITSM_VHost SET domain = ?, subdomain = ?, name = ?, 
				host = ?, registrar = ?, status = ?, renewCost = ?, notes = ?, registerDate = ?, 
				expireDate = ?, createDate = ?, createUser = ?, modifyDate = ?, modifyUser = ? WHERE id = ?");
				
			$put->bindParam(1, $this->domain);
			$put->bindParam(2, $this->subdomain);
			$put->bindParam(3, $this->name);
			$put->bindParam(4, $this->host);
			$put->bindParam(5, $this->registrar);
			$put->bindParam(6, $this->status);
			$put->bindParam(7, $this->renewCost);
			$put->bindParam(8, $this->notes);
			$put->bindParam(9, $this->registerDate);
			$put->bindParam(10, $this->expireDate);
			$put->bindParam(11, $this->createDate);
			$put->bindParam(12, $this->createUser);
			$put->bindParam(13, $this->lastModifyDate);
			$put->bindParam(14, $this->lastModifyUser);
			$put->bindParam(15, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		protected function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_VHost (domain, subdomain, name, host, registrar, 
				status, renewCost, notes, registerDate, expireDate, createDate, createUser, modifyDate,
				modifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				
			$post->bindParam(1, $this->domain);
			$post->bindParam(2, $this->subdomain);
			$post->bindParam(3, $this->name);
			$post->bindParam(4, $this->host);
			$post->bindParam(5, $this->registrar);
			$post->bindParam(6, $this->status);
			$post->bindParam(7, $this->renewCost);
			$post->bindParam(8, $this->notes);
			$post->bindParam(9, $this->registerDate);
			$post->bindParam(10, $this->expireDate);
			$post->bindParam(11, $this->createDate);
			$post->bindParam(12, $this->createUser);
			$post->bindParam(13, $this->lastModifyDate);
			$post->bindParam(14, $this->lastModifyUser);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
		}
		
		protected function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_VHost WHERE id = ?");
			$drop->bindParam(1, $this->id);
			$drop->execute();
			
			if($drop->rowCount() == 1)
			{
				unset($this->id);
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
			
			// Domain - is set
			if(ifSet($vars['domain']) === FALSE OR strlen($vars['domain']) == 0)
				$errs[] = "Domain Required";
			
			// Sub-Domain - is set
			if(ifSet($vars['subdomain']) === FALSE OR strlen($vars['subdomain']) == 0)
				$errs[] = "Subdomain Required";
			
			// Name - is set, is not longer than 64 chars
			if((ifSet($vars['name']) === FALSE) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			
			// Renew Cost - is set, is number, is not negative
			if(ifSet($vars['renewCost']) === FALSE)
				$errs[] = "Renew Cost Required";
			else if(!is_numeric($vars['renewCost']))
				$errs[] = "Renew Cost Must Be Numeric";
			else if($vars['renewCost'] < 0)
				$errs[] = "Renew Cost Must Be Positive";
			
			// Status - is set, is valid
			if(ifSet($vars['status']) === FALSE)
				$errs[] = "Status Required";
			else
			{
				$status = new \Attribute($vars['status']);
				
				if($status->load())
				{
					if($status->getExtension() != 'itsm' OR $status->getAttributeType() != 'wdns')
						$errs[] = "Status Is Invalid";
				}
				else
					$errs[] = "Status Is Invalid";
			}
			
			// Registrar Code - is set, is valid
			if(ifSet($vars['registrarCode']) === FALSE)
				$errs[] = "Registrar Required";
			else
			{
				$registrar = new Registrar();
				if(!$registrar->loadFromCode($vars['registrarCode']))
					$errs[] = "Registrar Not Found";
			}
			
			// Host IP Address 
			if(ifSet($vars['hostIp']) === FALSE)
				$errs[] = "Host I.P. Address Required";
			else
			{
				$host = new itsmcore\Host();
				
				if(!$host->loadFromIp($vars['hostIp']))
					$errs[] = "Host Not Found";
			}
			
			// Register Date - is set, is valid
			if((ifSet($vars['registerDate']) === FALSE) OR !$validator->validDate($vars['registerDate']))
				$errs[] = "Register Date Is Invalid";
			
			// Expire Date (optional) - is valid
			if(!empty($vars['expireDate']) AND !$validator->validDate($vars['expireDate']))
				$errs[] = "Expire Date Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		protected function update($vars)
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$registrar = new Registrar();
			$registrar->loadFromCode($vars['registrarCode']);
			$status = new \Attribute($vars['status']);
			$status->load();
			$host = new itsmcore\Host();
			$host->loadFromIP($vars['hostIp']);
			
			$this->domain = $vars['domain'];
			$this->subdomain = $vars['subdomain'];
			$this->name = $vars['name'];
			$this->registrar = $registrar->getId();
			$this->status = $status->getId();;
			$this->renewCost = $vars['renewCost'];
			$this->notes = $vars['notes'];
			$this->registerDate = $vars['registerDate'];
			$this->expireDate = (strlen($vars['expireDate']) == 0) ? NULL : $vars['expireDate'];
			$this->host = $host->getId();
			
			return TRUE;
		}
	}
