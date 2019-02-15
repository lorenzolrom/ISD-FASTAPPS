<?php namespace itsmcore;
	/**
	* Asset Purchase Order
	*/
	class PurchaseOrder extends ITSMItem
	{
		protected $number;
		protected $orderDate;
		protected $vendor;
		protected $status;
		protected $notes;
		protected $warehouse;
		protected $received;
		protected $receiveDate;
		protected $createUser;
		
		protected $sent;
		protected $sendDate;
		protected $canceled;
		protected $cancelDate;
		
		/////
		// GET-SET
		/////
		
		public function getNumber(){return $this->number;}
		public function getOrderDate(){return $this->orderDate;}
		public function getVendor(){return $this->vendor;}
		public function getStatus(){return $this->status;}
		public function getNotes(){return $this->notes;}
		public function getWarehouse(){return $this->warehouse;}
		public function getReceived(){return $this->received;}
		public function getReceiveDate(){return $this->receiveDate;}
		public function getCreateUser(){return $this->createUser;}
		
		public function getSent(){return $this->sent;}
		public function getSendDate(){return $this->sendDate;}
		public function getCanceled(){return $this->canceled;}
		public function getCancelDate(){return $this->cancelDate;}
		
		public function setNumber($number){$this->number = $number;}
		public function setOrderDate($orderDate){$this->orderDate = $orderDate;}
		public function setVendor($vendor){$this->vendor = $vendor;}
		public function setStatus($status){$this->status = $status;}
		public function setNotes($notes){$this->notes = $notes;}
		public function setWarehouse($warehouse){$this->warehouse = $warehouse;}
		public function setReceived($received){$this->received = $received;}
		public function setReceiveDate($receiveDate){$this->receiveDate = $receiveDate;}
		public function setCreateUser($createUser){$this->createUser = $createUser;}
		
		public function setSent($sent){$this->sent = $sent;}
		public function setSendDate($sendDate){$this->sendDate = $sendDate;}
		public function setCanceled($canceled){$this->canceled = $canceled;}
		public function setCancelDate($cancelDate){$this->cancelDate = $cancelDate;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$fetch = $conn->prepare("SELECT number, orderDate, warehouse, vendor, status, notes, createDate,
				createUser, received, receiveDate, lastModifyDate, lastModifyUser, sent, sendDate, canceled, cancelDate FROM ITSM_PurchaseOrder WHERE id = ? LIMIT 1");
				
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$purchaseOrder = $fetch->fetch();
			
			$this->number = $purchaseOrder['number'];
			$this->orderDate = $purchaseOrder['orderDate'];
			$this->warehouse = $purchaseOrder['warehouse'];
			$this->vendor = $purchaseOrder['vendor'];
			$this->status = $purchaseOrder['status'];
			$this->notes = $purchaseOrder['notes'];
			$this->createDate = $purchaseOrder['createDate'];
			$this->createUser = $purchaseOrder['createUser'];
			$this->received = $purchaseOrder['received'];
			$this->receiveDate = $purchaseOrder['receiveDate'];
			$this->lastModifyDate = $purchaseOrder['lastModifyDate'];
			$this->lastModifyUser = $purchaseOrder['lastModifyUser'];
			
			$this->sent = $purchaseOrder['sent'];
			$this->sendDate = $purchaseOrder['sendDate'];
			$this->canceled = $purchaseOrder['canceled'];
			$this->cancelDate = $purchaseOrder['cancelDate'];
			
			return TRUE;
		}
		
		private function fetchFromNumber()
		{
			if(!isset($this->number))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM ITSM_PurchaseOrder WHERE number = ? LIMIT 1");
			$fetch->bindParam(1, $this->number);
			
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO ITSM_PurchaseOrder (number, orderDate, warehouse, vendor,
				status, notes, createDate, createUser, received, receiveDate, lastModifyDate, lastModifyUser, sent, sendDate, canceled, cancelDate)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$post->bindParam(1, $this->number);
			$post->bindParam(2, $this->orderDate);
			$post->bindParam(3, $this->warehouse);
			$post->bindParam(4, $this->vendor);
			$post->bindParam(5, $this->status);
			$post->bindParam(6, $this->notes);
			$post->bindParam(7, $this->createDate);
			$post->bindParam(8, $this->createUser);
			$post->bindParam(9, $this->received);
			$post->bindParam(10, $this->receiveDate);
			$post->bindParam(11, $this->lastModifyDate);
			$post->bindParam(12, $this->lastModifyUser);
			$post->bindParam(13, $this->sent);
			$post->bindParam(14, $this->sendDate);
			$post->bindParam(15, $this->canceled);
			$post->bindParam(16, $this->cancelDate);
			
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
			
			$put = $conn->prepare("UPDATE ITSM_PurchaseOrder SET number = ?, orderDate = ?, warehouse = ?,
				vendor = ?, status = ?, notes = ?, createDate = ?, createUser = ?, received = ?,
				receiveDate = ?, lastModifyDate = ?, lastModifyUser = ?, sent = ?, sendDate = ?, canceled = ?, cancelDate = ? WHERE id = ?");
			
			$put->bindParam(1, $this->number);
			$put->bindParam(2, $this->orderDate);
			$put->bindParam(3, $this->warehouse);
			$put->bindParam(4, $this->vendor);
			$put->bindParam(5, $this->status);
			$put->bindParam(6, $this->notes);
			$put->bindParam(7, $this->createDate);
			$put->bindParam(8, $this->createUser);
			$put->bindParam(9, $this->received);
			$put->bindParam(10, $this->receiveDate);
			$put->bindParam(11, $this->lastModifyDate);
			$put->bindParam(12, $this->lastModifyUser);
			$put->bindParam(13, $this->sent);
			$put->bindParam(14, $this->sendDate);
			$put->bindParam(15, $this->canceled);
			$put->bindParam(16, $this->cancelDate);
			$put->bindParam(17, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM ITSM_PurchaseOrder WHERE id = ?");
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
			// Validation
			/////
			
			$validator = new \Validator();
			$warehouse = null;
			$vendor = null;
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
			
			// Order Date
			if(!$validator->validDate($vars['orderDate']))
				$errs[] = "Order Date Not Valid";
			
			if(!isset($vars['notes']) OR strlen($vars['notes']) == 0)
				$vars['notes'] = "";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET VARIABLES
			/////
			
			$this->vendor = $vendor->getId();
			$this->warehouse = $warehouse->getId();
			$this->orderDate = $vars['orderDate'];
			$this->notes = $vars['notes'];
			
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
			
			$this->number = getNextPurchaseOrderNumber();
			$this->createDate = date('Y-m-d');
			$this->createUser = $faCurrentUser->getId();
			$this->lastModifyDate = date('Y-m-d');
			$this->lastModifyUser = $faCurrentUser->getId();
			
			$this->sent = 0;
			$this->received = 0;
			$this->canceled = 0;
			
			$readyAttribute = new \Attribute();
			if(!$readyAttribute->loadFromCode('itsm', 'post', 'rdts'))
				throw new AppException("Could Not Set P.O. Status", "D09");
			
			// Set status to 'Ready to Sent'
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
		
		/**
		* Returns a list of commodities in this purchase order
		* @return List of entry rows, containing relationship id, commodity id, quantity, and unit cost
		*/
		public function getCommodities()
		{
			global $conn;
			
			$fetch = $conn->prepare("SELECT id, commodity, quantity, unitCost FROM ITSM_PurchaseOrder_Commodity WHERE purchaseOrder = ?");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			$commodities = [];
			
			foreach($fetch->fetchAll() as $commodity)
			{
				$commodities[] = $commodity;
			}
			
			return $commodities;
		}
		
		/**
		* Returns a list of cost items for this P.O.
		*/
		public function getCostItems()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT id, cost, notes FROM ITSM_PurchaseOrder_CostItem WHERE purchaseOrder = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$costItems = [];
			
			foreach($get->fetchAll() as $costItem)
			{
				$costItems[] = $costItem;
			}
			
			return $costItems;
		}
		
		/**
		* Assigns a commodity to this P.O.
		* @param numerical ID of commodity
		* @param quantity numerical quantity of items ordered
		* @param unitCost cost per item ordered
		* @return Was the add successful?
		*/
		public function addCommodity($commodityId, $quantity, $unitCost)
		{
			global $conn;
			
			$add = $conn->prepare("INSERT INTO ITSM_PurchaseOrder_Commodity (purchaseOrder, commodity, quantity, unitCost) VALUES (?, ?, ?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $commodityId);
			$add->bindParam(3, $quantity);
			$add->bindParam(4, $unitCost);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove a commodity from this P.O.
		*/
		public function removeCommodity($id)
		{
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_PurchaseOrder_Commodity WHERE id = ? AND purchaseOrder = ?");
			$remove->bindParam(1, $id);
			$remove->bindParam(2, $this->id);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		public function updateQuantity($id, $newQuantity)
		{
			global $conn;
			
			$update = $conn->prepare("UPDATE ITSM_PurchaseOrder_Commodity SET quantity = ? WHERE id = ? AND purchaseOrder = ?");
			$update->bindParam(1, $newQuantity);
			$update->bindParam(2, $id);
			$update->bindParam(3, $this->id);
			
			if($update->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Assigns a cost item to this P.O.
		*/
		public function addCostItem($cost, $notes)
		{
			global $conn;
			
			$add = $conn->prepare("INSERT INTO ITSM_PurchaseOrder_CostItem (purchaseOrder, cost, notes) VALUES (?, ?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $cost);
			$add->bindParam(3, $notes);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Remove a cost item from this P.O.
		*/
		public function removeCostItem($id)
		{
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM ITSM_PurchaseOrder_CostItem WHERE id = ?");
			$remove->bindParam(1, $id);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Mark purchase order as received and generate assets
		*/
		public function receive($receiveDate)
		{
			global $faCurrentUser;
			global $conn;
			
			$conn->beginTransaction();
			
			$commodities = $this->getCommodities();
			
			foreach($commodities as $commodityRow)
			{
				$asset = new Asset();
				
				$commodity = new Commodity($commodityRow['commodity']);
				$commodity->load();
				
				$asset->setCommodity($commodity->getId());
				$asset->setWarehouse($this->warehouse);
				$asset->setPurchaseOrder($this->getId());
				
				for($i = 0; $i < $commodityRow['quantity']; $i++)
				{
					if(!$asset->create())
					{
						$conn->rollback();
						return FALSE;
					}
				}
			}
			
			$this->received = 1;
			$this->receiveDate = $receiveDate;
			$lastModifyDate = date('Y-m-d');
			$lastModifyUser = $faCurrentUser->getId();
			
			if(!$this->save())
			{
				$conn->rollback();
				return FALSE;
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		*  Mark this purchase order as sent
		*/
		public function send()
		{
			// Cannot send canceled or received P.O.
			if($this->canceled == 1 OR $this->received == 1)
				return FALSE;
			
			if(sizeof($this->getCommodities()) == 0)
				return FALSE;
			
			$this->sent = 1;
			$this->sendDate = date('Y-m-d');
			
			$sentAttribute = new \Attribute();
			if(!$sentAttribute->loadFromCode('itsm', 'post', 'sent'))
				throw new AppException("Could Not Set P.O. Status", "D09");
			
			// Set status to 'Sent'
			$this->setStatus($sentAttribute->getId());
			
			return $this->save();
		}
		
		/**
		* Mark this purchase order as canceled
		*/
		public function cancel()
		{
			// Cannot cancel received or unsent P.O.
			if($this->received == 1 OR $this->sent == 0)
				return FALSE;
			
			$this->canceled = 1;
			$this->cancelDate = date('Y-m-d');
			
			$cancelAttribute = new \Attribute();
			if(!$cancelAttribute->loadFromCode('itsm', 'post', 'cncl'))
				throw new AppException("Could Not Set P.O. Status", "D09");
			
			// Set status to 'Canceled'
			$this->setStatus($cancelAttribute->getId());
			
			return $this->save();
		}
	}
