<?php namespace itsmwebmanager;

	/**
	* Return vhosts matching criteria
	* @param Filters
	* @return Array of VHost objects
	*/
	function getVHosts($domainFilter = "%", $subdomainFilter = "%", $nameFilter = "%", $hostAssetTagFilter = "%", 
		$registrarCodeFilter = "%", $statusFilter = [])
	{
		global $conn;
		
		$queryString = "SELECT id FROM ITSM_VHost WHERE domain LIKE ? AND subdomain LIKE ? 
			AND name LIKE ? AND host IN (SELECT id FROM ITSM_Host WHERE asset IN (SELECT id FROM ITSM_Asset WHERE 
			assetTag LIKE ?)) AND registrar IN (SELECT id FROM ITSM_Registrar WHERE code LIKE ?)";
		
		// Status Filter		
		if(!empty($statusFilter))
		{
			$statusList = [];
			
			foreach($statusFilter as $statusId)
			{
				if(ctype_digit($statusId))
					$statusList[] = $statusId;
			}
			
			$statusListString = implode("', '", $statusList);
			$queryString .= " AND status IN ('$statusListString')";
		}
		
		// Order by domain first, then subdomain
		$queryString .= " ORDER BY domain, subdomain DESC";
		
		$get = $conn->prepare($queryString);
		$get->bindParam(1, $domainFilter);
		$get->bindParam(2, $subdomainFilter);
		$get->bindParam(3, $nameFilter);
		$get->bindParam(4, $hostAssetTagFilter);
		$get->bindParam(5, $registrarCodeFilter);
		
		$get->execute();
		
		$domainNames = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $domainNameId)
		{
			$domainName = new VHost($domainNameId);
			
			if($domainName->load())
				$domainNames[] = $domainName;
		}
		
		return $domainNames;
	}
	
	/**
	* Return registrars matching criteria
	* @param Filters
	* @return Array of Registrar objects
	*/
	function getRegistrars($codeFilter = "%", $nameFilter = "%")
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM ITSM_Registrar WHERE code LIKE ? AND name LIKE ? ORDER BY code ASC");
		$get->bindParam(1, $codeFilter);
		$get->bindParam(2, $nameFilter);
		
		$get->execute();
		
		$registrars = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $registrarId)
		{
			$registrar = new Registrar($registrarId);
			
			if($registrar->load())
				$registrars[] = $registrar;
		}
		
		return $registrars;
	}
