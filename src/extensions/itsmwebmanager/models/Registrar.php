<?php namespace itsmwebmanager;
	/**
	* A domain name registrar
	*/
	class Registrar extends ITSMItem
	{
		private $code;
		private $url;
		private $phone;
		
		/////
		// GET-SET
		/////
		
		public function getCode(){return $this->code;}
		public function getURL(){return $this->url;}
		public function getPhone(){return $this->phone;}
		
		public function setCode($code){$this->code = $code;}
		public function setURL($url){$this->url = $url;}
		public function setPhone($phone){$this->phone = $phone;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		protected function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT code, name, url, phone, createDate, createUser, lastModifyDate, 
				lastModifyUser FROM ITSM_Registrar WHERE id = ? LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$registrar = $fetch->fetch();
			
			$this->code = $registrar['code'];
			$this->name = $registrar['name'];
			$this->url = $registrar['url'];
			$this->phone = $registrar['phone'];
			$this->createDate = $registrar['createDate'];
			$this->createUser = $registrar['createUser'];
			$this->lastModifyDate = $registrar['lastModifyDate'];
			$this->lastModifyUser = $registrar['lastModifyUser'];
			
			return TRUE;
		}
		
		protected function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Registrar WHERE code = ? LIMIT 1");
			$fetch->bindParam(1, $this->code);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			return $this->fetch();
		}
		
		protected function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE ITSM_Registrar SET code = ?, name = ?, url = ?, phone = ?, 
				createDate = ?, createUser = ?, lastModifyDate = ?, lastModifyUser = ? WHERE id = ?");
				
			$put->bindParam(1, $this->code);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->url);
			$put->bindParam(4, $this->phone);
			$put->bindParam(5, $this->createDate);
			$put->bindParam(6, $this->createUser);
			$put->bindParam(7, $this->lastModifyDate);
			$put->bindParam(8, $this->lastModifyUser);
			$put->bindParam(9, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		protected function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_Registrar (code, name, url, phone, createDate, createUser, 
				lastModifyDate, lastModifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->code);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->url);
			$post->bindParam(4, $this->phone);
			$post->bindParam(5, $this->createDate);
			$post->bindParam(6, $this->createUser);
			$post->bindParam(7, $this->lastModifyDate);
			$post->bindParam(8, $this->lastModifyUser);
			
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
			
			$drop = $conn->prepare("DELETE FROM ITSM_Registrar WHERE id = ?");
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
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$validator = new \Validator();
			$errs = [];
			
			// Code - is set, not more than 32 chars, not already in use
			if((ifSet($vars['code']) === FALSE) OR !$validator->validLength($vars['code'], 1, 32))
				$errs[] = "Code Must Be Between 1 And 32 Characters";
			else if(!ctype_alnum($vars['code']))
				$errs[] = "Code Must Contain Letters And Numbers Only";
			else
			{
				if($vars['code'] != $this->code)
				{
					$check = new Registrar();
					if($check->loadFromCode($vars['code']))
						$errs[] = "Code Already In Use";
				}
			}
			
			// Name - is set
			if(empty($vars['name']))
				$errs[] = "Name Required";
			
			// URL - is set
			if(empty($vars['url']))
				$errs[] = "URL Required";
			
			// Phone - is set, not more than 20 chars
			if((ifSet($vars['phone']) === FALSE) OR !$validator->validLength($vars['phone'], 1, 20))
				$errs[] = "Phone Must Be Between 1 And 20 Characters";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		protected function update($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			if(is_array($val))
				return $val;
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			$this->url = $vars['url'];
			$this->phone = $vars['phone'];
			
			return TRUE;
		}
		
		public function loadFromCode($code = FALSE)
		{
			if($code !== FALSE)
				$this->code = $code;
			
			return $this->fetchFromCode();
		}
	}
