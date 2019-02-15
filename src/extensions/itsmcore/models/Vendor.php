<?php namespace itsmcore;
	/**
	* Asset Vendor
	*/
	class Vendor extends ITSMItem
	{
		private $name;
		private $code;
		private $streetAddress;
		private $city;
		private $state;
		private $zipCode;
		private $phone;
		private $fax;
		private $createUser;
		
		/////
		// GET-SET
		/////
		
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		public function getStreetAddress(){return $this->streetAddress;}
		public function getCity(){return $this->city;}
		public function getState(){return $this->state;}
		public function getZipCode(){return $this->zipCode;}
		public function getPhone(){return $this->phone;}
		public function getFax(){return $this->fax;}
		public function getCreateUser(){return $this->createUser;}
		
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		public function setStreetAddress($streetAddress){$this->streetAddress = $streetAddress;}
		public function setCity($city){$this->city = $city;}
		public function setState($state){$this->state = $state;}
		public function setZipCode($zipCode){$this->zipCode = $zipCode;}
		public function setPhone($phone){$this->phone = $phone;}
		public function setFax($fax){$this->fax = $fax;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT name, code, streetAddress, city, state, zipCode, phone, fax, 
				createDate, createUser, lastModifyDate, lastModifyUser FROM ITSM_Vendor WHERE id = ? 
				LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$vendor = $fetch->fetch();
			
			$this->code = $vendor['code'];
			$this->name = $vendor['name'];
			$this->streetAddress = $vendor['streetAddress'];
			$this->city = $vendor['city'];
			$this->state = $vendor['state'];
			$this->zipCode = $vendor['zipCode'];
			$this->phone = $vendor['phone'];
			$this->fax = $vendor['fax'];
			$this->createDate = $vendor['createDate'];
			$this->createUser = $vendor['createUser'];
			$this->lastModifyDate = $vendor['lastModifyDate'];
			$this->lastModifyUser = $vendor['lastModifyUser'];
			
			return TRUE;
		}
		
		private function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Vendor WHERE code = ? LIMIT 1");
			$fetch->bindParam(1, $this->code);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_Vendor (code, name, streetAddress, city, state, zipCode, 
				phone, fax, createDate, createUser, lastModifyDate, lastModifyUser) VALUES (?, ?, ?, ?, ?, 
				?, ?, ?, ?, ?, ?, ?)");
				
			$post->bindParam(1, $this->code);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->streetAddress);
			$post->bindParam(4, $this->city);
			$post->bindParam(5, $this->state);
			$post->bindParam(6, $this->zipCode);
			$post->bindParam(7, $this->phone);
			$post->bindParam(8, $this->fax);
			$post->bindParam(9, $this->createDate);
			$post->bindParam(10, $this->createUser);
			$post->bindParam(11, $this->lastModifyDate);
			$post->bindParam(12, $this->lastModifyUser);
			
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
			
			$put = $conn->prepare("UPDATE ITSM_Vendor SET code = ?, name = ?, streetAddress = ?, city = ?, 
				state = ?, zipCode = ?, phone = ?, fax = ?, createDate = ?, createUser = ?, 
				lastModifyDate = ?, lastModifyUser = ? WHERE id = ?");
			
			$put->bindParam(1, $this->code);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->streetAddress);
			$put->bindParam(4, $this->city);
			$put->bindParam(5, $this->state);
			$put->bindParam(6, $this->zipCode);
			$put->bindParam(7, $this->phone);
			$put->bindParam(8, $this->fax);
			$put->bindParam(9, $this->createDate);
			$put->bindParam(10, $this->createUser);
			$put->bindParam(11, $this->lastModifyDate);
			$put->bindParam(12, $this->lastModifyUser);
			$put->bindParam(13, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			global $conn;
			
			if(!isset($this->id))
				return FALSE;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Vendor WHERE id = ?");
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
		
		private function validate($vars = [])
		{
			$validator = new \Validator();
			$errs = [];
			
			// Vendor code
			if((ifSet($vars['code']) === FALSE) OR !$validator->validLength($vars['code'], 1, 32))
				$errs[] = "Code Must Be Between 1 And 32 Characters";
			else if (!preg_match("/^[A-Za-z0-9-]+$/", $vars['code']))
				$errs[] = "Code Must Only Contain Letters, Numbers, And '-'";
			else
			{
				if($vars['code'] != $this->code)
				{
					$checkCode = new Vendor();
					
					if($checkCode->loadFromCode($vars['code']))
						$errs[] = "Code Already In Use";
				}
			}
			
			// Name
			if(strlen($vars['name']) == 0)
				$errs[] = "Name Required";
			
			// Street Address
			if(strlen($vars['streetAddress']) == 0)
				$errs[] = "Street Address Required";
			
			// City
			if(strlen($vars['city']) == 0)
				$errs[] = "City Required";
			
			// State
			if(strlen($vars['state']) != 2)
				$errs[] = "State Must Be 2 Characters";
			
			// Zip Code
			if(!ctype_digit($vars['zipCode']) OR !strlen($vars['zipCode']) == 5)
				$errs[] = "Zip Code Is Invalid";
			
			// Phone
			if((ifSet($vars['phone']) === FALSE) OR !$validator->validLength($vars['phone'], 1, 20))
				$errs[] = "Phone Number Must Be Between 1 And 20 Characters";
			
			// Fax (optional)
			if(strlen($vars['fax'])!= 0 AND !$validator->validLength($vars['fax'], 0, 20))
				$errs[] = "Fax Number Must Be Between 0 And 20 Characters";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		private function update($vars)
		{
			$val =  $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			if(strlen($vars['fax']) == 0)
				$vars['fax'] = "";
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			$this->streetAddress = $vars['streetAddress'];
			$this->city = $vars['city'];
			$this->state = $vars['state'];
			$this->zipCode = $vars['zipCode'];
			$this->phone = $vars['phone'];
			$this->fax = $vars['fax'];
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromCode($code = FALSE)
		{
			if($code !== FALSE)
			{
				$this->code = $code;
			}
			
			return $this->fetchFromCode();
		}
		
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->put();
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->post();
		}
		
		public function delete()
		{
			return $this->drop();
		}
		
		/**
		* Returns purchase orders for this Vendor
		*/
		public function getPurchaseOrders()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_PurchaseOrder WHERE vendor = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$purchaseOrders = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $purchaseOrderId)
			{
				$purchaseOrder = new PurchaseOrder($purchaseOrderId);
				if($purchaseOrder->load())
					$purchaseOrders[] = $purchaseOrder;
			}
			
			return $purchaseOrders;
		}
	}
