<?php namespace itsmcore;
	////////////////////
	// ISD-FastApps
	// (c) 2018 LLR Technologies / Info. Systems Development
	// ITSMCore Extension Function File
	////////////////////
	
	/**
	* Returned next asset tag in series
	*/
	function getNextAssetTag()
	{
		global $conn;
		
		$get = $conn->prepare("SELECT assetTag FROM ITSM_Asset ORDER BY CAST(assetTag AS UNSIGNED) DESC LIMIT 1");
		$get->execute();
		
		if($get->rowCount() == 0)
			return 1;
		else
			return $get->fetchColumn() + 1;
	}
	
	/**
	* Return next purchase order number in series
	*/
	function getNextPurchaseOrderNumber()
	{
		global $conn;
		
		$get = $conn->prepare("SELECT number FROM ITSM_PurchaseOrder ORDER BY number DESC LIMIT 1");
		$get->execute();
		
		if($get->rowCount() == 0)
			return 1;
		else
			return $get->fetchColumn() + 1;
	}
	
	/**
	* Return next return order number in series
	*/
	function getNextReturnOrderNumber()
	{
		global $conn;
		
		$get = $conn->prepare("SELECT number FROM ITSM_ReturnOrder ORDER BY number DESC LIMIT 1");
		$get->execute();
		
		if($get->rowCount() == 0)
			return 1;
		else
			return $get->fetchColumn() + 1;
	}
	
	/**
	* Returns a list of all warehouses
	* @param includeClosed Should closed warehouses be included?
	*/
	function getWarehouses($includeClosed = FALSE)
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_Warehouse";
		
		if(!$includeClosed)
			$queryString .= " WHERE closed = 0";
		
		$queryString .= " ORDER BY code ASC";
				
		$get = $conn->prepare($queryString);
		$get->execute();
		
		$warehouses = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $warehouseId)
		{
			$warehouse = new Warehouse($warehouseId);
			
			if($warehouse->load())
				$warehouses[] = $warehouse;
		}
		
		return $warehouses;
	}
	
	/**
	* Returns a list of all vendors
	*/
	function getVendors()
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM ITSM_Vendor ORDER BY code ASC");
		$get->execute();
		
		$vendors = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $vendorId)
		{
			$vendor = new Vendor($vendorId);
			
			if($vendor->load())
			{
				$vendors[] = $vendor;
			}
		}
		
		return $vendors;
	}
	
	/**
	* Return commodities matching filters
	*/
	function getCommodities($codeFilter = "%", $nameFilter = "%", $manufacturerFilter = "%", $modelFilter = "%", $unitCostFilter = "%", $commodityTypeFilter = [], $assetTypeFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_Commodity WHERE code LIKE ? AND name LIKE ? AND manufacturer LIKE ? AND model LIKE ? AND unitCost LIKE ?";
		
		// Convert type filters to arrays
		$commodityTypeList = [];
		$assetTypeList = [];
		
		if(!empty($commodityTypeFilter))
		{
			foreach($commodityTypeFilter as $commodityTypeId)
			{
				if(ctype_digit($commodityTypeId))
					$commodityTypeList[] = $commodityTypeId;
			}
			
			// Convert type filter arrays to strings
			$commodityTypeListString = implode("', '", $commodityTypeList);
			
			$queryString .= " AND commodityType IN ('$commodityTypeListString')";
		}
		
		if(!empty($assetTypeFilter))
		{
			foreach($assetTypeFilter as $assetTypeId)
			{
				if(ctype_digit($assetTypeId))
					$assetTypeList[] = $assetTypeId;
			}
			
			$assetTypeListString = implode("', '", $assetTypeList);
			$queryString .= " AND assetType IN ('$assetTypeListString')";
		}
		
		$queryString .= " ORDER BY code ASC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(1, $codeFilter);
		$get->bindParam(2, $nameFilter);
		$get->bindParam(3, $manufacturerFilter);
		$get->bindParam(4, $modelFilter);
		$get->bindParam(5, $unitCostFilter);
		
		$get->execute();
		
		$commodities = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $commodityId)
		{
			$commodity = new Commodity($commodityId);
			
			if($commodity->load())
				$commodities[] = $commodity;
		}
		
		return $commodities;
	}
	
	/**
	* Returns a list of purchase orders matching filters
	*/
	function getPurchaseOrders($numberFilter = "%", $vendorCodeFilter = "%", $orderStartDate = "", $orderEndDate = "", $warehouseCodeFilter = "%", $statusFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_PurchaseOrder WHERE number LIKE ? AND vendor IN (SELECT id FROM ITSM_Vendor WHERE code LIKE ?) AND orderDate > ? AND orderDate < ? 
			AND warehouse IN (SELECT id FROM ITSM_Warehouse WHERE code LIKE ?)";
		
		// Validate order dates
		$validator = new \Validator();
		
		if(!$validator->validDate($orderStartDate))
			$orderStartDate = "1000-01-01";
		
		if(!$validator->validDate($orderEndDate))
			$orderEndDate = "9999-12-31";
		
		// Convert status filter to query string
		$statusList = [];
		
		if(!empty($statusFilter))
		{
			foreach($statusFilter as $statusId)
			{
				if(ctype_digit($statusId))
					$statusList[] = $statusId;
			}
			
			$statusListString = implode("', '", $statusList);
			
			$queryString .= " AND status IN ('$statusListString')";
		}
		
		$queryString .= " ORDER BY number DESC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(1, $numberFilter);
		$get->bindParam(2, $vendorCodeFilter);
		$get->bindParam(3, $orderStartDate);
		$get->bindParam(4, $orderEndDate);
		$get->bindParam(5, $warehouseCodeFilter);
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
	
	/**
	* Returns a list of Assets matching criteria
	*/
	function getAssets($assetTagFilter = "%", $serialNumberFilter = "%", $inWarehouse = [], $discardedFilter = [], $verifiedFilter = [], $buildingCodeFilter = "%", 
		$locationCodeFilter = "%", $warehouseCodeFilter = "%", $manufacturerFilter = "%", $modelFilter = "%", $purchaseOrderNumberFilter = "%", 
		$commodityCodeFilter = "%", $commodityNameFilter = "%", $commodityTypeFilter = [], $assetTypeFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_Asset WHERE assetTag LIKE :assetTag 
			AND IFNULL(serialNumber, '') LIKE :serialNumber 
			AND commodity IN (SELECT id FROM ITSM_Commodity WHERE manufacturer LIKE :manufacturer) 
			AND commodity IN (SELECT id FROM ITSM_Commodity WHERE model LIKE :model) 
			AND commodity IN (SELECT id FROM ITSM_Commodity WHERE code LIKE :commodityCode) 
			AND commodity IN (SELECT id FROM ITSM_Commodity WHERE name LIKE :commodityName)";
		
		// Check for warehouse?
		if($warehouseCodeFilter != "%" AND $warehouseCodeFilter != "%%")
		{
			$checkWarehouse = TRUE;
			$queryString .= " AND IFNULL(warehouse, '') IN (SELECT id FROM ITSM_Warehouse WHERE code LIKE :warehouse)";
		}
		
		// Check for building?
		if($buildingCodeFilter != "%" AND $buildingCodeFilter != "%%")
		{
			$checkBuilding = TRUE;
			$queryString .= " AND IFNULL(location, '') IN (SELECT id FROM FacilitiesCore_Location WHERE building IN (SELECT id FROM FacilitiesCore_Building WHERE code LIKE :building))";
		}
		
		// Check for location?
		if($locationCodeFilter != "%" AND $locationCodeFilter != "%%")
		{
			$checkLocation = TRUE;
			$queryString .= " AND IFNULL(location, '') IN (SELECT id FROM FacilitiesCore_Location WHERE code LIKE :location)";
		}
		
		// Check for purchase order?
		if($purchaseOrderNumberFilter != "%" AND $purchaseOrderNumberFilter != "%%")
		{
			$checkPO = TRUE;
			$queryString .= " AND purchaseOrder IN (SELECT id FROM ITSM_PurchaseOrder WHERE number LIKE :po)";
		}
		
		// In Warehouse?
		if(!empty($inWarehouse) AND sizeof($inWarehouse) == 1)
		{
			if(in_array(0, $inWarehouse))
				$queryString .= " AND warehouse IS NULL";
			if(in_array(1, $inWarehouse))
				$queryString .= " AND warehouse IS NOT NULL";
		}
		
		// Discarded?
		if(!empty($discardedFilter))
		{
			$discardedArray = [];
			
			foreach($discardedFilter as $discardedValue)
			{
				if($discardedValue == 1 OR $discardedValue == 0)
					$discardedArray[] = $discardedValue;
			}
			
			$queryString .= " AND discarded IN ('" . implode("' ,'", $discardedArray) . "')";
		}
		
		// Verified?
		if(!empty($verifiedFilter))
		{
			$verifiedArray = [];
			
			foreach($verifiedFilter as $verifiedValue)
			{
				if($verifiedValue == 1 OR $verifiedValue == 0)
					$verifiedArray[] = $verifiedValue;
			}
			
			$queryString .= " AND verified IN ('" . implode("' ,'", $verifiedArray) . "')";
		}
		
		// Commodity Type Filter
		if(!empty($commodityTypeFilter))
		{
			$commodityTypeArray = [];
			
			foreach($commodityTypeFilter as $commodityType)
			{
				if(ctype_digit($commodityType))
					$commodityTypeArray[] = $commodityType;
			}
			
			$queryString .= " AND commodity IN (SELECT id FROM ITSM_Commodity WHERE commodityType IN ('" . implode("', '", $commodityTypeArray) . "'))";
		}
		
		// Asset Type Filter
		if(!empty($assetTypeFilter))
		{
			$assetTypeArray = [];
			
			foreach($assetTypeFilter as $assetType)
			{
				if(ctype_digit($assetType))
					$assetTypeArray[] = $assetType;
			}
			
			$queryString .= " AND commodity IN (SELECT id FROM ITSM_Commodity WHERE assetType IN ('" . implode("', '", $assetTypeArray) . "'))";
		}
		
		$queryString .= " ORDER BY assetTag DESC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(':assetTag', $assetTagFilter);
		$get->bindParam(':serialNumber', $serialNumberFilter);
		$get->bindParam(':manufacturer', $manufacturerFilter);
		$get->bindParam(':model', $modelFilter);
		$get->bindParam(':commodityCode', $commodityCodeFilter);
		$get->bindParam(':commodityName', $commodityNameFilter);
		if(isset($checkBuilding))
			$get->bindParam(':building', $buildingCodeFilter);
		if(isset($checkLocation))
			$get->bindParam(':location', $locationCodeFilter);
		if(isset($checkWarehouse))
			$get->bindParam(':warehouse', $warehouseCodeFilter);
		if(isset($checkPO))
			$get->bindParam(':po', $purchaseOrderNumberFilter);
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
	* Get a list of Host objects matching filters
	*/
	function getHosts($assetTagFilter = "%", $ipAddressFilter = "%", $macAddressFilter = "%", $systemNameFilter = "%",
		$systemCPUFilter = "%", $systemRAMFilter = "%", $systemOSFilter = "%", $systemDomainFilter = "%")
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM ITSM_Host WHERE asset IN (SELECT id FROM ITSM_Asset WHERE assetTag LIKE ?) 
			AND ipAddress LIKE ? AND macAddress LIKE ? AND systemName LIKE ? AND IFNULL(systemCPU, '') LIKE ? 
			AND IFNULL(systemRAM, '') LIKE ? AND IFNULL(systemOS, '') LIKE ? AND IFNULL(systemDomain, '') LIKE ? ORDER BY ipAddress ASC");
		
		$get->bindParam(1, $assetTagFilter);
		$get->bindParam(2, $ipAddressFilter);
		$get->bindParam(3, $macAddressFilter);
		$get->bindParam(4, $systemNameFilter);
		$get->bindParam(5, $systemCPUFilter);
		$get->bindParam(6, $systemRAMFilter);
		$get->bindParam(7, $systemOSFilter);
		$get->bindParam(8, $systemDomainFilter);
		
		$get->execute();
		
		$hosts = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $hostId)
		{
			$host = new Host($hostId);
			if($host->load())
			{
				$hosts[] = $host;
			}
		}
		
		return $hosts;
	}
	
	/**
	* Returns a list of return orders matching filters
	*/
	function getReturnOrders($numberFilter = "%", $vendorRMAFilter = "%", $vendorCodeFilter = "%", $orderStartDate = "", $orderEndDate = "", $warehouseCodeFilter = "%", $typeFilter = [], $statusFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_ReturnOrder WHERE number LIKE ? AND vendorRMA LIKE ? AND vendor IN (SELECT id FROM ITSM_Vendor WHERE code LIKE ?) AND orderDate > ? AND orderDate < ? 
			AND warehouse IN (SELECT id FROM ITSM_Warehouse WHERE code LIKE ?)";
		
		// Validate order dates
		$validator = new \Validator();
		
		if(!$validator->validDate($orderStartDate))
			$orderStartDate = "1000-01-01";
		
		if(!$validator->validDate($orderEndDate))
			$orderEndDate = "9999-12-31";
		
		// Convert status filter to query string
		$statusList = [];
		
		if(!empty($statusFilter))
		{
			foreach($statusFilter as $statusId)
			{
				if(ctype_digit($statusId))
					$statusList[] = $statusId;
			}
			
			$statusListString = implode("', '", $statusList);
			
			$queryString .= " AND status IN ('$statusListString')";
		}
		
		// Convert type filter to query string
		$typeList = [];
		
		if(!empty($typeFilter))
		{
			foreach($typeFilter as $typeId)
			{
				if(ctype_digit($typeId))
					$typeList[] = $typeId;
			}
			
			$typeListString = implode("', '", $typeList);
			
			$queryString .= " AND type IN ('$typeListString')";
		}
		
		$queryString .= " ORDER BY number DESC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(1, $numberFilter);
		$get->bindParam(2, $vendorRMAFilter);
		$get->bindParam(3, $vendorCodeFilter);
		$get->bindParam(4, $orderStartDate);
		$get->bindParam(5, $orderEndDate);
		$get->bindParam(6, $warehouseCodeFilter);
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
	* Attempts to ping the supplied address
	*/
	function ping($ipAddress)
	{
		exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($ipAddress)), $res, $val);
		
		return $val === 0;
	}	
