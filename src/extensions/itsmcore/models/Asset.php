<?php namespace itsmcore;
	/**
	* Asset
	*/
	class Asset extends ITSMItem
	{
		private $commodity;
		private $warehouse;
		private $assetTag;
		private $parentId;
		private $location;
		private $serialNumber;
		private $manufactureDate;
		private $purchaseOrder;
		private $notes;
		private $discarded;
		private $discardDate;
		private $verified;
		private $verifyDate;
		private $verifyUser;
		
		/////
		// GET-SET
		/////
		
		public function getCommodity(){return $this->commodity;}
		public function getWarehouse(){return $this->warehouse;}
		public function getAssetTag(){return $this->assetTag;}
		public function getParent(){return $this->parentId;}
		public function getLocation(){return $this->location;}
		public function getSerialNumber(){return $this->serialNumber;}
		public function getManufactureDate(){return $this->manufactureDate;}
		public function getPurchaseOrder(){return $this->purchaseOrder;}
		public function getDiscarded(){return $this->discarded;}
		public function getDiscardDate(){return $this->discardDate;}
		public function getNotes(){return $this->notes;}
		public function getVerified(){return $this->verified;}
		public function getVerifyDate(){return $this->verifyDate;}
		
		public function setCommodity($commodity){$this->commodity = $commodity;}
		public function setWarehouse($warehouse){$this->warehouse = $warehouse;}
		public function setAssetTag($assetTag){$this->assetTag = $assetTag;}
		public function setLocation($location){$this->location = $location;}
		public function setSerialNumber($serialNumber){$this->serialNumber = $serialNumber;}
		public function setManufactureDate($manufactureDate){$this->manufactureDate = $manufactureDate;}
		public function setPurchaseOrder($purchaseOrder){$this->purchaseOrder = $purchaseOrder;}
		public function setDiscarded($discarded){$this->discarded = $discarded;}
		public function setDiscardDate($discardDate){$this->discardDate = $discardDate;}
		public function setNotes($notes){$this->notes = $notes;}
		public function setVerified($verified){$this->verified = $verified;}
		public function setVerifyDate($verifyDate){$this->verifyDate = $verifyDate;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		/**
		* Fetch Asset attributes from database
		* @param Was the fetch successful?
		*/
		private function fetch()
		{
			global $conn;
			
			if(!isset($this->id))
				return FALSE;
			
			$fetch = $conn->prepare("SELECT commodity, warehouse, assetTag, parent, location, serialNumber,
				manufactureDate, purchaseOrder, notes, createDate, discarded, discardDate, lastModifyDate, 
				lastModifyUser, verified, verifyDate, verifyUser FROM ITSM_Asset WHERE id = ? LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$asset = $fetch->fetch();
			
			$this->commodity = $asset['commodity'];
			$this->warehouse = $asset['warehouse'];
			$this->assetTag = $asset['assetTag'];
			$this->parentId = $asset['parent'];
			$this->location = $asset['location'];
			$this->serialNumber = $asset['serialNumber'];
			$this->manufactureDate = $asset['manufactureDate'];
			$this->purchaseOrder = $asset['purchaseOrder'];
			$this->notes = $asset['notes'];
			$this->createDate = $asset['createDate'];
			$this->discarded = $asset['discarded'];
			$this->discardDate = $asset['discardDate'];
			$this->lastModifyDate = $asset['lastModifyDate'];
			$this->lastModifyUser = $asset['lastModifyUser'];
			$this->verified = $asset['verified'];
			$this->verifyDate = $asset['verifyDate'];
			$this->verifyUser = $asset['verifyUser'];
			
			return TRUE;
		}
		
		/**
		* Fetch Asset attributes using Asset Tag
		* @param Was the fetch successful?
		*/
		private function fetchFromAssetTag()
		{
			if(!isset($this->assetTag))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_Asset WHERE assetTag = ? LIMIT 1");
			$fetch->bindParam(1, $this->assetTag);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		/**
		* Update database with current attributes
		* @return Was the put successful?
		*/
		private function put()
		{
			global $conn;
			
			if(!isset($this->id))
				return FALSE;
			
			$put = $conn->prepare("UPDATE ITSM_Asset SET commodity = ?, warehouse = ?,
				assetTag = ?, location = ?, serialNumber = ?, manufactureDate = ?,
				purchaseOrder = ?, notes = ?, createDate = ?, discarded = ?, discardDate = ?, 
				lastModifyDate = ?, lastModifyUser = ?, verified = ?, verifyDate = ?, verifyUser = ?, parent = ? WHERE id = ?");
				
			$put->bindParam(1, $this->commodity);
			$put->bindParam(2, $this->warehouse);
			$put->bindParam(3, $this->assetTag);
			$put->bindParam(4, $this->location);
			$put->bindParam(5, $this->serialNumber);
			$put->bindParam(6, $this->manufactureDate);
			$put->bindParam(7, $this->purchaseOrder);
			$put->bindParam(8, $this->notes);
			$put->bindParam(9, $this->createDate);
			$put->bindParam(10, $this->discarded);
			$put->bindParam(11, $this->discardDate);
			$put->bindParam(12, $this->lastModifyDate);
			$put->bindParam(13, $this->lastModifyUser);
			$put->bindParam(14, $this->verified);
			$put->bindParam(15, $this->verifyDate);
			$put->bindParam(16, $this->verifyUser);
			$put->bindParam(17, $this->parentId);
			$put->bindParam(18, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Create a new record in the table
		* @return Was the post successful?
		*/
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_Asset (commodity, warehouse, assetTag, location,
				serialNumber, manufactureDate, purchaseOrder, notes, createDate, discarded, discardDate,
				lastModifyDate, lastModifyUser, verified, verifyDate, verifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->commodity);
			$post->bindParam(2, $this->warehouse);
			$post->bindParam(3, $this->assetTag);
			$post->bindParam(4, $this->location);
			$post->bindParam(5, $this->serialNumber);
			$post->bindParam(6, $this->manufactureDate);
			$post->bindParam(7, $this->purchaseOrder);
			$post->bindParam(8, $this->notes);
			$post->bindParam(9, $this->createDate);
			$post->bindParam(10, $this->discarded);
			$post->bindParam(11, $this->discardDate);
			$post->bindParam(12, $this->lastModifyDate);
			$post->bindParam(13, $this->lastModifyUser);
			$post->bindParam(14, $this->verified);
			$post->bindParam(15, $this->verifyDate);
			$post->bindParam(16, $this->verifyUser);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		/**
		* Drop the record from the database
		*/
		private function drop()
		{
			global $conn;
			
			if(!isset($this->id))
				return FALSE;
			
			$drop = $conn->prepare("DELETE FROM ITSM_Asset WHERE id = ?");
			
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
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new \Validator();
			$errs = [];
			
			// Asset Tag
			if(!isset($vars['assetTag']) OR strlen($vars['assetTag']) == 0)
				$errs[] = "Asset Tag Required";
			else if(!ctype_digit($vars['assetTag']))
				$errs[] = "Asset Tag Can Only Contain Numbers";
			else
			{
				// Asset Tag must be unique
				if($vars['assetTag'] != $this->assetTag)
				{
					$checkAssetTag = new Asset();
					
					if($checkAssetTag->loadFromAssetTag($vars['assetTag']))
						$errs[] = "Asset Tag Already In Use";
				}
			}
			
			// Serial Number
			if(isset($vars['serialNumber']) AND !$validator->validLength($vars['serialNumber'], 0, 64))
				$errs[] = "Serial Number Must Be Between 0 And 64 Characters";
			
			// Manufacture Date
			if(isset($vars['manufactureDate']) AND !$validator->validLength($vars['manufactureDate'], 0, 64))
				$errs[] = "Manufacture Date Must Be Between 0 And 64 Characters";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET ATTRIBUTES
			/////
			
			$this->assetTag = $vars['assetTag'];
			$this->serialNumber = $vars['serialNumber'];
			$this->manufactureDate = $vars['manufactureDate'];
			$this->notes = $vars['notes'];
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromAssetTag($assetTag = FALSE)
		{
			if($assetTag !== FALSE)
				$this->assetTag = $assetTag;
			
			return $this->fetchFromAssetTag();
		}
		
		/**
		* Create a new asset using current attributes
		*/
		public function create()
		{
			global $faCurrentUser;
			
			$this->verified = 0;
			$this->discarded = 0;
			$this->assetTag = getNextAssetTag();
			$this->createDate = date('Y-m-d');
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->post();
		}
		
		/**
		* Save changes to this Asset
		*/
		public function save($vars = [])
		{
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			return $this->put();
		}
		
		/**
		* Mark an Asset as discarded
		*/
		public function discard($discardChildren = FALSE)
		{
			if($this->getReturnOrder() !== FALSE)
				return FALSE;
			
			if(!empty($this->getHosts())) // Cannot discard asset with hosts
				return FALSE;
			
			global $faCurrentUser;
			
			$this->discarded = 1;
			$this->discardDate = date('Y-m-d');
			
			$this->location = NULL;
			$this->warehouse = NULL;
			
			foreach($this->getChildren() as $child)
			{
				if($discardChildren)
					$child->discard(TRUE);
				else
					$child->unsetParent();
			}
			
			return $this->save();
		}
		
		/**
		* Mark an Asset as deleted
		*/
		public function delete()
		{			
			global $faCurrentUser;
			
			return $this->drop();
		}
		
		/**
		* Assigns this asset to a location.
		* Removes it from a warehouse
		*/
		public function assignLocation($locationCode, $overrideReturn = FALSE)
		{
			if($this->getReturnOrder() !== FALSE AND $overrideReturn === FALSE)
				return FALSE;
			
			if($this->discarded == 1)
				return FALSE;
			
			$this->warehouse = NULL;
			$this->location = $locationCode;
						
			foreach($this->getChildren() as $child)
			{
				$child->assignLocation($locationCode, $overrideReturn);
			}
			
			return $this->save();
		}
		
		/**
		* Returns this asset to a warehouse
		*/
		public function returnToWarehouse($warehouseCode, $overrideReturn = FALSE)
		{			
			if($this->getReturnOrder() !== FALSE AND $overrideReturn === FALSE)
				return FALSE;
			
			if($this->discarded == 1)
				return FALSE;
			
			$this->location = NULL;
			$this->warehouse = $warehouseCode;
			
			foreach($this->getChildren() as $child)
			{
				$child->returnToWarehouse($warehouseCode, $overrideReturn);
			}
			
			return $this->save();
		}
		
		/**
		* Marks this asset as verified today
		*/
		public function verify()
		{
			global $faCurrentUser;
			
			$this->verified = 1;
			$this->verifyDate = date('Y-m-d');
			$this->verifyUser = $faCurrentUser->getId();
			
			return $this->save();
		}
		
		/**
		* Marks this asset as unverified
		*/
		public function unverify()
		{
			$this->verified = 0;
			$this->verifyDate = NULL;
			$this->verifyUser = NULL;
			
			return $this->save();
		}
		
		/**
		* Returns if this asset is in the Asset Worksheet
		*/
		public function isInWorksheet()
		{
			global $conn;
			$verify = $conn->prepare("SELECT asset FROM ITSM_Asset_Worksheet WHERE asset = ? LIMIT 1");
			$verify->bindParam(1, $this->id);
			$verify->execute();
			
			if($verify->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Returns list of Hosts tied to this asset
		*/
		public function getHosts()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_Host WHERE asset = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$hosts = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $hostId)
			{
				$host = new Host($hostId);
				if($host->load())
					$hosts[] = $host;
			}
			
			return $hosts;
		}
		
		/**
		* Returns if this asset is on an active return order
		*/
		public function getReturnOrder()
		{
			global $conn;
			
			$find = $conn->prepare("SELECT number FROM ITSM_ReturnOrder WHERE id IN (SELECT returnOrder FROM ITSM_ReturnOrder_Asset 
				WHERE asset = ?) AND received = 0 AND canceled = 0 LIMIT 1");
				
			$find->bindParam(1, $this->id);
			$find->execute();
			
			if($find->rowCount() == 1)
				return $find->fetchColumn();
			
			return FALSE;
		}
		
		/**
		* Returns list of all return orders this asset has been on
		*/
		public function getReturnOrders()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM ITSM_ReturnOrder WHERE id IN (SELECT returnOrder FROM ITSM_ReturnOrder_Asset WHERE asset = ?)");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$returnOrders = [];
			
			foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $returnOrderId)
			{
				$returnOrder = new ReturnOrder($returnOrderId);
				if($returnOrder->load())
					$returnOrders[] = $returnOrder;
			}
			
			return $returnOrders;
		}
		
		/**
		* Sets this asset's parent and assigns its location/warehouse to the parent asset's
		*/
		public function setParent($parentAssetTag)
		{
			if(!isset($this->id))
				return FALSE;
			
			$parent = new Asset();
			
			if($this->discarded == 1)
				return ['Cannot Assign Discarded Asset'];
			
			// Make sure parent exists
			if(!$parent->loadFromAssetTag($parentAssetTag))
				return ['Parent Asset Not Found'];
			
			// Make sure we're not assigning this asset to be a child of itself
			if($parent->getId() == $this->id)
				return ['Cannot Assign Asset To Itself'];
			
			// Make sure parent is not a child of this asset
			if(sizeof($this->getChildren($parent->getId())) !== 0)
				return ['Cannot Assign Parent Asset To Child'];
			
			if($parent->getDiscarded() == 1)
				return ['Cannot Assign To Discarded Asset'];
			
			// Set this asset location/warehouse to be the same as the parent
			// Also sets the parent ID
			$this->warehouse = $parent->getWarehouse();
			$this->location = $parent->getLocation();
			$this->parentId = $parent->getId();
			
			return $this->save();
		}
		
		/**
		* Removes the parent
		*/
		public function unsetParent()
		{
			if(!isset($this->id))
				return FALSE;
			
			$this->parentId = NULL;
			
			return $this->save();
		}
		
		/**
		* Get assets who have this as a parent
		*/
		public function getChildren($childId = FALSE)
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$q = "SELECT id FROM ITSM_Asset WHERE parent = ?";
			
			if($childId !== FALSE)
				$q .= " AND id = ?";
			
			$g = $conn->prepare($q);
			$g->bindParam(1, $this->id);
			
			if($childId !== FALSE)
				$g->bindParam(2, $childId);
			
			$g->execute();
			
			$children = [];
			
			foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $childId)
			{
				$child = new Asset($childId);
				if($child->load())
					$children[] = $child;
			}
			
			return $children;
		}
	}
