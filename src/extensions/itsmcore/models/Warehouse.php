<?php namespace itsmcore;
	/**
	* Asset Storage Warehouse
	*/
	class Warehouse extends ITSMItem
	{
		private $code;
		private $name;
		private $createUser;

		/////
		// GET-SET
		/////
		
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		public function getCreateUser(){return $this->createUser;}
		
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
		
			global $conn;
			
			$fetch = $conn->prepare("SELECT code, name, createDate, createUser, lastModifyDate, lastModifyUser FROM ITSM_Warehouse WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$warehouse = $fetch->fetch();
			
			$this->code = $warehouse['code'];
			$this->name = $warehouse['name'];
			$this->createDate = $warehouse['createDate'];
			$this->createUser = $warehouse['createUser'];
			$this->lastModifyDate = $warehouse['lastModifyDate'];
			$this->lastModifyUser = $warehouse['lastModifyUser'];
			
			return TRUE;
		}
		
		private function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Warehouse WHERE code = ? LIMIT 1");
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
			
			$post = $conn->prepare("INSERT INTO ITSM_Warehouse (code, name, createDate, createUser, 
				lastModifyDate, lastModifyUser) VALUES (?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->code);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->createDate);
			$post->bindParam(4, $this->createUser);
			$post->bindParam(5, $this->lastModifyDate);
			$post->bindParam(6, $this->lastModifyUser);
			
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
			
			$put = $conn->prepare("UPDATE ITSM_Warehouse SET code = ?, name = ?, createDate = ?, createUser = ?, 
				lastModifyDate = ?, lastModifyUser = ? WHERE id = ?");
			
			$put->bindParam(1, $this->code);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->createDate);
			$put->bindParam(4, $this->createUser);
			$put->bindParam(5, $this->lastModifyDate);
			$put->bindParam(6, $this->lastModifyUser);
			$put->bindParam(7, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			global $conn;
			if(!isset($this->id))
				return FALSE;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Warehouse WHERE id = ?");
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
		
		private function update($vars)
		{
			/////
			// VALIDATION
			/////
			
			$validator = new \Validator();
			$errs = [];
			
			if((isset($vars['code']) === FALSE) OR !$validator->validLength($vars['code'], 1, 32))
				$errs[] = "Code Must Be Between 1 And 32 Characters";
			else if (!preg_match("/^[A-Za-z0-9-]+$/", $vars['code']))
				$errs[] = "Code Must Only Contain Letters, Numbers, And '-'";
			else
			{
				if($vars['code'] != $this->code)
				{
					$testCode = new Warehouse();

					if($testCode->loadFromCode($vars['code']))
						$errs[] = "Code Already In Use";
				}
			}
			
			if((ifSet($vars['name']) === FALSE) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET VARIABLES
			/////
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromCode($code = FALSE)
		{
			if($code !== FALSE)
				$this->code = $code;
			
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
		* Gets all assets currently stored in this warehouse
		* @return Array of Asset objects
		*/
		public function getAssets()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_Asset WHERE warehouse = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$assets = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $assetId)
			{
				$asset = new Asset($assetId);
				if($asset->load())
					$assets[] = $asset;
			}
			
			return $assets;
		}
		
		/**
		* Closes the warehouse and transfers all assets to the specified warehouse
		*/
		public function close($newWarehouseId = FALSE)
		{
			global $faCurrentUser;
			global $conn;
			
			$conn->beginTransaction();
			
			// If warehouse has assets
			if(sizeof($this->getAssets()) != 0)
			{
				// Must have new warehouse to proceed
				if($newWarehouseId === FALSE)
					return FALSE;
				
				$newWarehouse = new Warehouse($newWarehouseId);
				
				if(!$newWarehouse->load())
					return FALSE;
				
				foreach($this->getAssets() as $asset)
				{
					$asset->setWarehouse($newWarehouse->getId());
					
					if(!$asset->save())
					{
						$conn->rollback();
						return FALSE;
					}
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
		* Returns purchase orders for this Warehouse
		*/
		public function getPurchaseOrders()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_PurchaseOrder WHERE warehouse = ?");
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
