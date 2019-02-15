<?php namespace itsmcore;
	/**
	* Asset Return Order
	*/
	class ReturnOrder extends PurchaseOrder
	{
		private $type;
		private $vendorRMA;
		
		/////
		// GET-SET
		/////
		
		public function getReturnType(){return $this->type;}
		public function getVendorRMA(){return $this->vendorRMA;}
		
		public function setReturnType($type){$this->type = $type;}
		public function setVendorRMA($vendorRMA){$this->vendorRMA = $vendorRMA;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT number, type, vendorRMA, orderDate, vendor, status, notes,
				warehouse, received, receiveDate, createDate, createUser, lastModifyDate, lastModifyUser,
				sent, sendDate, canceled, cancelDate FROM ITSM_ReturnOrder WHERE id = ? LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$returnOrder = $fetch->fetch();
			
			$this->number = $returnOrder['number'];
			$this->type = $returnOrder['type'];
			$this->vendorRMA = $returnOrder['vendorRMA'];
			$this->orderDate = $returnOrder['orderDate'];
			$this->vendor = $returnOrder['vendor'];
			$this->status = $returnOrder['status'];
			$this->notes = $returnOrder['notes'];
			$this->warehouse = $returnOrder['warehouse'];
			$this->received = $returnOrder['received'];
			$this->receiveDate = $returnOrder['receiveDate'];
			$this->createDate = $returnOrder['createDate'];
			$this->createUser = $returnOrder['createUser'];
			$this->lastModifyDate = $returnOrder['lastModifyDate'];
			$this->lastModifyUser = $returnOrder['lastModifyUser'];
			$this->sent = $returnOrder['sent'];
			$this->sendDate = $returnOrder['sendDate'];
			$this->canceled = $returnOrder['canceled'];
			$this->cancelDate = $returnOrder['cancelDate'];
			
			return TRUE;
		}
		
		private function fetchFromNumber()
		{
			if(!isset($this->number))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_ReturnOrder WHERE number = ? LIMIT 1");
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
			
			$put = $conn->prepare("UPDATE ITSM_ReturnOrder SET number = ?, type = ?, vendorRMA = ?, 
				orderDate = ?, vendor = ?, status = ?, notes = ?, warehouse = ?, received = ?, 
				receiveDate = ?, createDate = ?, createUser = ?, lastModifyDate = ?, lastModifyUser = ?, 
				sent = ?, sendDate = ?, canceled = ?, cancelDate = ? WHERE id = ?");
				
			$put->bindParam(1, $this->number);
			$put->bindParam(2, $this->type);
			$put->bindParam(3, $this->vendorRMA);
			$put->bindParam(4, $this->orderDate);
			$put->bindParam(5, $this->vendor);
			$put->bindParam(6, $this->status);
			$put->bindParam(7, $this->notes);
			$put->bindParam(8, $this->warehouse);
			$put->bindParam(9, $this->received);
			$put->bindParam(10, $this->receiveDate);
			$put->bindParam(11, $this->createDate);
			$put->bindParam(12, $this->createUser);
			$put->bindParam(13, $this->lastModifyDate);
			$put->bindParam(14, $this->lastModifyUser);
			$put->bindParam(15, $this->sent);
			$put->bindParam(16, $this->sendDate);
			$put->bindParam(17, $this->canceled);
			$put->bindParam(18, $this->cancelDate);
			$put->bindParam(19, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_ReturnOrder (number, type, vendorRMA, orderDate, 
				vendor, status, notes, warehouse, received, receiveDate, createDate, createUser, 
				lastModifyDate, lastModifyUser) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				
			$post->bindParam(1, $this->number);
			$post->bindParam(2, $this->type);
			$post->bindParam(3, $this->vendorRMA);
			$post->bindParam(4, $this->orderDate);
			$post->bindParam(5, $this->vendor);
			$post->bindParam(6, $this->status);
			$post->bindParam(7, $this->notes);
			$post->bindParam(8, $this->warehouse);
			$post->bindParam(9, $this->received);
			$post->bindParam(10, $this->receiveDate);
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
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_ReturnOrder WHERE id = ?");
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
			$vendor = null;
			$warehouse = null;
			$errs = [];
			
			// Vendor Code
			if(strlen($vars['vendorCode']) == 0)
				$errs[] = "Vendor Code Required";
			else
			{
				$vendor = new Vendor();
				if(!$vendor->loadFromCode($vars['vendorCode']))
					$errs[] = "Vendor Code Is Invalid";
			}
			
			// Warehouse Code
			if(strlen($vars['warehouseCode']) == 0)
				$errs[] = "Warehouse Code Required";
			else
			{
				$warehouse = new Warehouse();
				if(!$warehouse->loadFromCode($vars['warehouseCode']))
					$errs[] = "Warehouse Is Invalid";
			}
			
			// Type
			if(ifSet($vars['returnType']) === FALSE)
				$errs[] = "Return Type Required";
			else
			{
				$attribute = new \Attribute($vars['returnType']);
				if($attribute->load())
				{
					if($attribute->getExtension() != "itsm" OR $attribute->getAttributeType() != "roty")
						$errs[] = "Return Type Is Invalid";
				}
				else
					$errs[] = "Return Type Is Invalid";
			}
			
			// Order Date
			if(!$validator->validDate($vars['orderDate']))
				$errs[] = "Order Date Not Valid";
			
			// Notes (optional)
			if(!isset($vars['notes']) OR strlen($vars['notes']) == 0)
				$vars['notes'] = "";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET ATTRIBUTES
			/////
			
			$this->orderDate = $vars['orderDate'];
			$this->vendor = $vendor->getId();
			$this->type = $vars['returnType'];
			$this->warehouse = $warehouse->getId();
			$this->notes = $vars['notes'];
			$this->vendorRMA = $vars['vendorRMA'];
			$this->returnType = $vars['returnType'];
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromNumber($number = FALSE)
		{
			if($number !== FALSE)
				$this->number = $number;
			
			return $this->fetchFromNumber();
		}
		
		public function create($vars = [])
		{
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			global $faCurrentUser;
			
			$this->number = getNextReturnOrderNumber();
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			$this->sent = 0;
			$this->received = 0;
			$this->canceled = 0;
			
			$readyAttribute = new \Attribute();
			if(!$readyAttribute->loadFromCode('itsm', 'rost', 'rdts'))
				throw new AppException("Could Not Set R.O. Status", "D09");
			
			// Set status to 'Ready to Send'
			$this->setStatus($readyAttribute->getId());
			
			return $this->post();
		}
		
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
		
		public function delete()
		{
			if($this->sent == 1 OR $this->received == 1 OR $this->canceled == 1)
				return FALSE;
			
			return $this->drop();
		}
		
		public function getAssets()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$getAssets = $conn->prepare("SELECT asset FROM ITSM_ReturnOrder_Asset WHERE returnOrder = ?");
			$getAssets->bindParam(1, $this->id);
			$getAssets->execute();
			
			$assets = [];
			
			foreach($getAssets->fetchAll(\PDO::FETCH_COLUMN, 0) as $assetId)
			{
				$asset = new Asset($assetId);
				if($asset->load())
				{
					$assets[] = $asset;
				}
			}
			
			return $assets;
		}
		
		/**
		* Returns a list of cost items for this R.O.
		*/
		public function getCostItems()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id, cost, notes FROM ITSM_ReturnOrder_CostItem WHERE returnOrder = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$costItems = [];
			
			foreach($get->fetchAll() as $costItem)
			{
				$costItems[] = $costItem;
			}
			
			return $costItems;
		}
		
		public function addAsset($assetId)
		{
			global $conn;
			
			$this->removeAsset($assetId);
			
			$add = $conn->prepare("INSERT INTO ITSM_ReturnOrder_Asset (returnOrder, asset) VALUES (?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $assetId);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		public function removeAsset($assetId)
		{
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_ReturnOrder_Asset WHERE returnOrder = ? AND asset = ?");
			$remove->bindParam(1, $this->id);
			$remove->bindParam(2, $assetId);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Assigns a cost item to this R.O.
		*/
		public function addCostItem($cost, $notes)
		{
			global $conn;
			
			$add = $conn->prepare("INSERT INTO ITSM_ReturnOrder_CostItem (returnOrder, cost, notes) VALUES (?, ?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $cost);
			$add->bindParam(3, $notes);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove a cost item from this R.O.
		*/
		public function removeCostItem($id)
		{
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_ReturnOrder_CostItem WHERE id = ?");
			$remove->bindParam(1, $id);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		public function send()
		{
			global $conn;
			
			// Cannot send canceled or received R.O.
			if($this->canceled == 1 OR $this->received == 1)
				return FALSE;
			
			if(sizeof($this->getAssets()) == 0)
				return FALSE;
			
			$sentAttribute = new \Attribute();
			if(!$sentAttribute->loadFromCode('itsm', 'rost', 'sent'))
				throw new AppException("Could Not Set R.O. Status", "D09");
			
			// Set status to 'Sent'
			$this->status = $sentAttribute->getId();
			
			// Return all assets to warehouse
			$conn->beginTransaction();
			
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->returnToWarehouse($this->warehouse, TRUE))
				{
					$conn->rollback();
					return FALSE;
				}
				
				// Set warehouse to null
				$asset->setWarehouse(NULL);
				$asset->save();
			}
			
			$this->sendDate = date('Y-m-d');
			$this->sent = 1;
			
			if($this->save()) // Save R.O.
			{
				$conn->commit();
				return TRUE;
			}
			else
				$conn->rollback();
			
			return FALSE;
		}
		
		public function cancel()
		{
			global $conn;
			
			// Cannot cancel received or unsent R.O.
			if($this->received == 1 OR $this->sent == 0)
				return FALSE;
			
			$this->canceled = 1;
			$this->cancelDate = date('Y-m-d');
			
			$cancelAttribute = new \Attribute();
			if(!$cancelAttribute->loadFromCode('itsm', 'rost', 'cncl'))
				throw new AppException("Could Not Set R.O. Status", "D09");
			
			// Set status to 'Canceled'
			$this->setStatus($cancelAttribute->getId());
			
			// Return all assets to warehouse
			$conn->beginTransaction();
			
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->returnToWarehouse($this->warehouse, TRUE))
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			if($this->save()) // Save R.O.
			{
				$conn->commit();
				return TRUE;
			}
			else
				$conn->rollback();
			
			return FALSE;
		}
		
		public function receive($receiveDate)
		{
			global $conn;
			if($this->received == 1 OR $this->canceled == 1)
				return FALSE;
			
			$this->received = 1;
			$this->receiveDate = date('Y-m-d');
			
			$receivedAttribute = new \Attribute();
			if(!$receivedAttribute->loadFromCode('itsm', 'rost', 'rcvd'))
				throw new AppException("Could Not Set R.O. Status", "D09");
			
			// Set status to 'Received'
			$this->setStatus($receivedAttribute->getId());
			
			$conn->beginTransaction();
			
			// Mark all assets as in warehouse
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->returnToWarehouse($this->warehouse, TRUE))
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			if($this->save()) // Save R.O.
			{
				$conn->commit();
				return TRUE;
			}
			else
				$conn->rollback();
			
			return FALSE;
		}
	}
