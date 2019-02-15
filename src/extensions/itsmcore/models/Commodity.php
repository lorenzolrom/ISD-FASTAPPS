<?php namespace itsmcore;
	/**
	* Commodity
	*/
	class Commodity extends ITSMItem
	{
		private $code;
		private $name;
		private $commodityType;
		private $assetType;
		private $manufacturer;
		private $model;
		private $unitCost;
		private $createUser;
		
		/////
		// GET-SET
		/////
		
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		public function getAssetType(){return $this->assetType;}
		public function getCommodityType(){return $this->commodityType;}
		public function getManufacturer(){return $this->manufacturer;}
		public function getModel(){return $this->model;}
		public function getUnitCost(){return $this->unitCost;}
		public function getCreateUser(){return $this->createUser;}
		
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		public function setCommodityType($commodityType){$this->commodityType = $commodityType;}
		public function setAssetType($assetType){$this->assetType = $assetType;}
		public function setManufacturer($manufacturer){$this->manufacturer = $manufacturer;}
		public function setModel($model){$this->model = $model;}
		public function setUnitCost($unitCost){$this->unitCost = $unitCost;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT code, name, assetType, commodityType, manufacturer, model, unitCost, createUser, createDate,
				lastModifyDate, lastModifyUser FROM ITSM_Commodity WHERE id = ? LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$commodity = $fetch->fetch();
			
			$this->code = $commodity['code'];
			$this->name = $commodity['name'];
			$this->assetType = $commodity['assetType'];
			$this->commodityType = $commodity['commodityType'];
			$this->manufacturer = $commodity['manufacturer'];
			$this->model = $commodity['model'];
			$this->unitCost = $commodity['unitCost'];
			$this->createDate = $commodity['createDate'];
			$this->createUser = $commodity['createUser'];
			$this->lastModifyDate = $commodity['lastModifyDate'];
			$this->lastModifyUser = $commodity['lastModifyUser'];
			
			return TRUE;
		}
		
		private function fetchFromCode()
		{
			if(!isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Commodity WHERE code = ? LIMIT 1");
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
			
			$post = $conn->prepare("INSERT INTO ITSM_Commodity (code, name, assetType, commodityType, manufacturer, model,
				unitCost, createUser, createDate, lastModifyDate, lastModifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				
			$post->bindParam(1, $this->code);
			$post->bindParam(2, $this->name);
			$post->bindParam(3, $this->assetType);
			$post->bindParam(4, $this->commodityType);
			$post->bindParam(5, $this->manufacturer);
			$post->bindParam(6, $this->model);
			$post->bindParam(7, $this->unitCost);
			$post->bindParam(8, $this->createUser);
			$post->bindParam(9, $this->createDate);
			$post->bindParam(10, $this->lastModifyDate);
			$post->bindParam(11, $this->lastModifyUser);
			
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
			
			$put = $conn->prepare("UPDATE ITSM_Commodity SET code = ?, name = ?, assetType = ?, commodityType = ?, manufacturer = ?,
				model = ?, unitCost = ?, createUser = ?, createDate = ?, lastModifyDate = ?, lastModifyUser = ? WHERE id = ?");
			
			$put->bindParam(1, $this->code);
			$put->bindParam(2, $this->name);
			$put->bindParam(3, $this->assetType);
			$put->bindParam(4, $this->commodityType);
			$put->bindParam(5, $this->manufacturer);
			$put->bindParam(6, $this->model);
			$put->bindParam(7, $this->unitCost);
			$put->bindParam(8, $this->createUser);
			$put->bindParam(9, $this->createDate);
			$put->bindParam(10, $this->lastModifyDate);
			$put->bindParam(11, $this->lastModifyUser);
			$put->bindParam(12, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Commodity WHERE id = ?");
			$drop->bindParam(1, $this->id);
			$drop->execute();
			
			if($drop->rowCount() != 1)
				return FALSE;
			
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		private function update($vars)
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new \Validator();
			$errs = [];
			
			// Code
			if(!ifSet($vars['code']) OR !$validator->validLength($vars['code'], 1, 32))
				$errs[] = "Code Must Be Between 1 And 32 Characters";
			else if (!preg_match("/^[A-Za-z0-9-]+$/", $vars['code']))
				$errs[] = "Code Must Only Contain Letters, Numbers, And '-'";
			else
			{
				if($vars['code'] != $this->code)
				{
					$checkCode = new Commodity();
					
					if($checkCode->loadFromCode($vars['code']))
						$errs[] = "Code Already In Use";
				}
			}
			
			// Name
			if(!ifSet($vars['name']) OR !$validator->validLength($vars['name'], 1, 64))
				$errs[] = "Name Must Be Between 1 And 64 Characters";
					
			// Commodity Type
			if(!ifSet($vars['commodityType']))
				$errs[] = "Commodity Type Required";
			else
			{
				$commodityType = new \Attribute($vars['commodityType']);
				if(!$commodityType->load() OR !$validator->isValidAttribute('itsm', 'coty', $commodityType->getCode()))
					$errs[] = "Commodity Type Is Invalid";
			}
			
			// Asset Type
			if(!ifSet($vars['assetType']))
				$errs[] = "Asset Type Required";
			else
			{
				$assetType = new \Attribute($vars['assetType']);
				if(!$assetType->load() OR !$validator->isValidAttribute('itsm', 'asty', $assetType->getCode()))
					$errs[] = "Asset Type Is Invalid";
			}
			
			// Manufacturer
			if(strlen($vars['manufacturer']) == 0)
				$errs[] = "Manufacturer Required";
			
			// Model
			if(strlen($vars['model']) == 0)
				$errs[] = "Model Required";
			
			// Unit Cost
			if(strlen($vars['unitCost']) == 0)
				$errs[] = "Unit Cost Required";
			else if(!is_numeric($vars['unitCost']))
				$errs[] = "Unit Cost Must Be Numeric";
			else if($vars['unitCost'] < 0)
				$errs[] = "Unit Cost Must Be Positive";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET VARIABLES
			/////
			
			$this->code = $vars['code'];
			$this->name = $vars['name'];
			$this->commodityType = $vars['commodityType'];
			$this->assetType = $vars['assetType'];
			$this->manufacturer = $vars['manufacturer'];
			$this->model = $vars['model'];
			$this->unitCost = $vars['unitCost'];
			
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
		
		public function delete()
		{
			return $this->drop();
		}
	}
