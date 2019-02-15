<?php namespace itsmcore;
	/**
	* The global asset worksheet
	*/
	class AssetWorksheet
	{
		public function __construct(){}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		/**
		* Returns count of all assets in worksheet
		*/
		public function getCount()
		{
			global $conn;
			$count = $conn->prepare("SELECT asset FROM ITSM_Asset_Worksheet");
			$count->execute();
			
			return $count->rowCount();
		}
		
		/**
		* Returns if the given asset ID is in the worksheet
		*/
		public function hasAsset($assetId)
		{
			global $conn;
			$check = $conn->prepare("SELECT asset FROM ITSM_Asset_Worksheet WHERE asset = ? LIMIT 1");
			$check->bindParam(1, $assetId);
			$check->execute();
			
			if($check->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Returns list of Asset objects that are in the W.S.
		*/
		public function getAssets()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT asset FROM ITSM_Asset_Worksheet");
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
		* Removes all assets from the worksheet
		*/
		public function removeAll()
		{
			global $conn;
			
			$get = $conn->prepare("DELETE FROM ITSM_Asset_Worksheet");
			$get->execute();
		}
		
		/**
		* Add individual asset to the worksheet
		* @param assetId numerical ID of asset
		*/
		public function addAsset($assetId)
		{
			if($this->hasAsset($assetId))
				return FALSE;
			
			global $conn;
			$add = $conn->prepare("INSERT INTO ITSM_Asset_Worksheet (asset) VALUES (?)");
			$add->bindParam(1, $assetId);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Removes individual asset from the worksheet
		* @param assetId numerical ID of asset
		*/
		public function removeAsset($assetId)
		{
			global $conn;
			$remove = $conn->prepare("DELETE FROM ITSM_Asset_Worksheet WHERE asset = ?");
			$remove->bindParam(1, $assetId);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BULK OPERATIONS
		/////
		
		/**
		* Assign all assets in W.S. to the specified location
		*/
		public function assignToLocation($locationCode)
		{
			global $conn;
			
			$conn->beginTransaction();
			
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->assignLocation($locationCode))
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Return all assets in W.S. to the specified warehouse
		*/
		public function returnToWarehouse($warehouseCode)
		{
			global $conn;
			
			$conn->beginTransaction();
			
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->returnToWarehouse($warehouseCode))
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		/**
		* Mark all assets in W.S. as discarded
		*/
		public function discard()
		{
			global $conn;
			
			$conn->beginTransaction();
			
			foreach($this->getAssets() as $asset)
			{
				if(!$asset->discard())
				{
					$conn->rollback();
					return FALSE;
				}
			}
			
			$conn->commit();
			$this->removeAll();
			return TRUE;
		}
		
		/**
		* Mark all assets in W.S. as verified
		*/
		public function verify()
		{
			foreach($this->getAssets() as $asset)
			{
				$asset->verify();
			}
		}
		
		/**
		* Mark all assets in W.S. as unverified
		*/
		public function unverify()
		{
			foreach($this->getAssets() as $asset)
			{
				$asset->unverify();
			}
		}
	}
