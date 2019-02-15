<?php namespace itsmservices;

	/**
	* Return the next available application number
	*/
	function getNextApplicationNumber()
	{
		global $conn;
		
		$get = $conn->prepare("SELECT number FROM ITSM_Application ORDER BY number DESC LIMIT 1");
		$get->execute();
		
		if($get->rowCount() == 0)
			return 1;
		
		return $get->fetchColumn() + 1;
	}
	
	/**
	* Returns appliations matching filters
	* @param Search filters
	* @return Array of Application objects in descending number order
	*/
	function getApplications($numberFilter = "%", $nameFilter = "%", $descriptionFilter = "%", 
		$ownerUsernameFilter = "%", $typeFilter = [], $publicFacingFilter = [], $lifeExpectancyFilter = [], 
		$dataVolumeFilter = [], $authTypeFilter = [], $portFilter = "%", $hostFilter = "%", $vhostFilter = "%", $statusFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT ITSM_Application.id FROM ITSM_Application WHERE number LIKE :number AND name LIKE :name AND 
			description LIKE :description AND owner IN (SELECT id FROM User WHERE username LIKE :username) 
			AND port LIKE :port";
		
		// VHost
		if($vhostFilter != "%" AND $vhostFilter != "%%")
		{
			$queryString .= " AND id IN (SELECT application FROM ITSM_Application_VHost WHERE vhost IN (SELECT id FROM ITSM_VHost WHERE domain LIKE :vhost OR subdomain LIKE :vhost))";
		}
		
		if($hostFilter != "%" AND $hostFilter != "%%")
		{
			$queryString .= " AND id IN (SELECT application FROM ITSM_Application_Host WHERE host IN 
			(SELECT id FROM ITSM_Host WHERE ipAddress LIKE :host OR systemName LIKE :host OR asset IN 
			(SELECT id FROM ITSM_Asset WHERE assetTag LIKE :host)))";
		}
		
		// Type
		if(!empty($typeFilter))
		{
			$typeList = [];
			
			foreach($typeFilter as $typeId)
			{
				if(ctype_digit($typeId))
					$typeList[] = $typeId;
			}
			
			$typeListString = implode("', '", $typeList);
			$queryString .= " AND type IN ('$typeListString')";
		}
		
		// Public Facing
		if(!empty($publicFacingFilter) AND sizeof($publicFacingFilter) == 1)
		{
			if(in_array(0, $publicFacingFilter))
				$queryString .= " AND publicFacing = 0";
			else if(in_array(1, $publicFacingFilter))
				$queryString .= " AND publicFacing = 1";
		}
		
		// Life Expectancy
		if(!empty($lifeExpectancyFilter))
		{
			$list = [];
			
			foreach($lifeExpectancyFilter as $id)
			{
				if(ctype_digit($id))
					$list[] = $id;
			}
			
			$list = implode("', '", $list);
			$queryString .= " AND lifeExpectancy IN ('$list')";
		}
		
		// Data Volume
		if(!empty($dataVolumeFilter))
		{
			$list = [];
			
			foreach($dataVolumeFilter as $id)
			{
				if(ctype_digit($id))
					$list[] = $id;
			}
			
			$list = implode("', '", $list);
			$queryString .= " AND dataVolume IN ('$list')";
		}
		
		// Auth Type
		if(!empty($authTypeFilter))
		{
			$list = [];
			
			foreach($authTypeFilter as $id)
			{
				if(ctype_digit($id))
					$list[] = $id;
			}
			
			$list = implode("', '", $list);
			$queryString .= " AND authType IN ('$list')";
		}
		
		// Status
		if(!empty($statusFilter))
		{
			$list = [];
			
			foreach($statusFilter as $id)
			{
				if(ctype_digit($id))
					$list[] = $id;
			}
			
			$list = implode("', '", $list);
			$queryString .= " AND status IN ('$list')";
		}
		
		$queryString .= " ORDER BY number DESC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(':number', $numberFilter);
		$get->bindParam(':name', $nameFilter);
		$get->bindParam(':description', $descriptionFilter);
		$get->bindParam(':username', $ownerUsernameFilter);
		$get->bindParam(':port', $portFilter);
		if($hostFilter != "%" AND $hostFilter != "%%")
			$get->bindParam(':host', $hostFilter);
		if($vhostFilter != "%" AND $vhostFilter != "%%")
			$get->bindParam(':vhost', $vhostFilter);
		$get->execute();
		
		$applications = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $applicationId)
		{
			$application = new Application($applicationId);
			
			if($application->load())
				$applications[] = $application;
		}
		
		return $applications;
	}
