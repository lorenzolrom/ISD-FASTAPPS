<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// Core Function File
	////////////////////
	
	/**
	* @return string The current page URI, omits GET variables
	*/
	function getURI()
	{
		return rtrim(explode('?', $_SERVER['REQUEST_URI'], 2)[0], "/");
	}
	
	/*
	* This is to avoid undefined index warnings.
	* If the supplied value is a boolean, it is returned without modification
	* @return The variable provided if it is set, and false if it is not.
	*/
	function ifSet(&$string)
	{
		if(isset($string))
			return $string;
		return FALSE;
	}
	
	/**
	* Returns the supplied string surrounded by '%' wildcars
	* @param $string string The original string
	* @return string Wildcarded string
	*/
	function wildcard($string)
	{
		if($string === FALSE)
			return FALSE;
		
		return "%" . $string . "%";
	}
	
	/**
	* @return array Page objects containing all sections
	* @param $useWeight boolean should sections be sorted by weight
     * @throws AppException in the event database query fails
	*/
	function getSections($useWeight = FALSE)
	{
		global $conn;
		
		$query = "SELECT id FROM Page WHERE type = 'sect'";
		
		if($useWeight)
		{
			$query .= " ORDER BY weight ASC";
		}
		
		$sections = [];
		
		// Get list of all sections
		$getSections = $conn->query($query);
		
		foreach($getSections->fetchAll(PDO::FETCH_COLUMN, 0) as $section_id)
		{
			$section = new Page($section_id);
			
			if($section->load())
			{
				$sections[] = $section;
			}
		}
		
		return $sections;
	}
	
	/**
	* Returns an array of Users
	* @param $disabledFilter array List of values for the user's 'disabled' attribute
     * @param $usernameFilter string Filter for username
     * @param $lastNameFilter string Filter for last name
     * @param $firstNameFilter string Filter for first name
	* @return array of User objects
     * @throws AppException in the event user query fails
	*/
	function getUsers($disabledFilter = [], $usernameFilter = "", $lastNameFilter = "", $firstNameFilter = "")
	{
		global $conn;
		
		// Build query
		$query = "SELECT id FROM User"; // Base query
		
		if(!is_array($disabledFilter)) // If a single value is set, convert it to the only value in an array
		{
			$disabledValue = $disabledFilter;
			
			unset($disabledFilter);
			$disabledFilter[] = $disabledValue;
		}
		
		if(!empty($disabledFilter))
		{
			$query .= " WHERE disabled IN ('" . implode("','", $disabledFilter) . "')"; // Convert array to MYSQL array
		}
		else
		{
			$query .= " WHERE disabled IN ('%')";
		}
		
		if(!empty($usernameFilter) AND $usernameFilter !== FALSE)
			$query .= " AND username LIKE :username";
		
		if(!empty($lastNameFilter) AND $lastNameFilter !== FALSE)
			$query .= " AND lastName LIKE :lastName";
		
		if(!empty($firstNameFilter) AND $firstNameFilter !== FALSE)
			$query .= " AND firstName LIKE :firstName";
		
		$get = $conn->prepare($query);
		$get->bindParam(':username', $usernameFilter);
		$get->bindParam(':firstName', $firstNameFilter);
		$get->bindParam(':lastName', $lastNameFilter);
		
		$get->execute();
		
		$users = [];
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $user_id)
		{
			$thisUser = new User($user_id);
			if($thisUser->load())
				$users[] = $thisUser;
		}
		
		return $users;
	}
	
	/**
	* Returns a list of roles
	* @param $nameFilter string A filter for what names to get, if not set is ignored
	* @return array List of role objects
     * @throws AppException
	*/
	function getRoles($nameFilter = "")
	{
		global $conn;
		
		$roles = [];
		
		$query = "SELECT id FROM Role";
		
		if(!empty($nameFilter))
			$query .= " WHERE name LIKE :name";
		
		$query .= " ORDER BY name ASC";
		
		$get = $conn->prepare($query);
		$get->bindParam(':name', $nameFilter);
		$get->execute();
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $role_id)
		{
			$role = new Role($role_id);
			if($role->load())
				$roles[] = $role;
		}
		
		return $roles;
	}
	
	/**
	* @return array An array of permission codes, sorted alphabetically
     * @throws AppException
	*/
	function getPermissions()
	{
		global $conn;
		
		$permissions = $conn->query("SELECT code FROM Permission ORDER BY code ASC");
		return $permissions->fetchAll(PDO::FETCH_COLUMN, 0);
	}
	
	/**
	* @return A list of token objects matching the search criteria
	* @param username Username to matching
	* @param ipAddress IP address to match
	* @param startDate Earliest date for records
	* @param endDate Latest date for records
     * @throws AppException
	*/
	function getTokens($username, $ipAddress, $startDate, $endDate)
	{
		global $conn;
		
		$get = $conn->prepare("SELECT token FROM Token WHERE user IN (SELECT id FROM User WHERE username LIKE ?) AND ipAddress LIKE ? AND issueTime >= ? AND expireTime <= ? ORDER BY issueTime DESC");
		$get->bindParam(1, $username);
		$get->bindParam(2, $ipAddress);
		$get->bindValue(3, $startDate . " 00:00:00");
		$get->bindValue(4, $endDate . " 23:59:59");
		
		$get->execute();
		
		$tokens = [];
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $tokenToken)
		{
			$token = new Token($tokenToken);
			if($token->load())
				$tokens[] = $token;
		}
		
		return $tokens;
	}
	
	/**
	* @return A list of notifications matching the search critera
	* @param username Username to match
	* @param title Title to match
	* @startDate Start date limit
	* @endDate End date limit
	*/
	function getNotifications($username, $title, $startDate, $endDate)
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM Notification WHERE user IN (SELECT id FROM User WHERE username LIKE ?) AND title LIKE ? AND time >= ? AND time <= ? ORDER BY time DESC");
		$get->bindParam(1, $username);
		$get->bindParam(2, $title);
		$get->bindValue(3, $startDate . " 00:00:00");
		$get->bindValue(4, $endDate . " 23:59:59");
		
		$get->execute();
		
		$notifications = [];
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $notificationId)
		{
			$notification = new Notification($notificationId);
			if($notification->load())
				$notifications[] = $notification;
		}
		
		return $notifications;
	}
	
	/**
	* Returns an array of pages matching criteria
	* @param $type Type of page, defaults to all pages
	*/
	function getPages($type = "")
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM Page WHERE type LIKE ? ORDER BY weight, url, title ASC");
		
		if(empty($type))
			$get->bindValue(1, "%");
		else
			$get->bindParam(1, $type);
		
		$get->execute();
		
		$pages = [];
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $pageId)
		{
			$page = new Page($pageId);
			if($page->load())
				$pages[] = $page;
		}
		
		return $pages;
	}
	
	/**
	* Returns a list of Attributes matching filters
	*/
	function getAttributes($extensionFilter = "%", $typeFilter = "%", $codeFilter = "%", $nameFilter = "%")
	{
		global $conn;
		
		$get = $conn->prepare("SELECT id FROM Attribute WHERE extension LIKE ? AND type LIKE ? AND code LIKE ? AND name LIKE ? ORDER BY name ASC");
		
		$get->bindParam(1, $extensionFilter);
		$get->bindParam(2, $typeFilter);
		$get->bindParam(3, $codeFilter);
		$get->bindParam(4, $nameFilter);
		
		$get->execute();
		
		$attributes = [];
		
		foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $attributeId)
		{
			$attribute = new Attribute($attributeId);
			if($attribute->load())
				$attributes[] = $attribute;
		}
		
		return $attributes;
	}
	
	/**
	* Return bulletins matching filters
	* @return List of Bulletin objects
	*/
	function getBulletins($startDateFilter = "1000-01-01", $endDateFilter = "9999-12-31", $titleFilter = "%", $inactiveFilter = [])
	{
		global $conn;
		
		$q = "SELECT id FROM Bulletin WHERE title LIKE ? AND startDate >= ? AND endDate <= ?";
		
		if(!is_array($inactiveFilter)) // If a single value is set, convert it to the only value in an array
		{
			$inactiveValue = $inactiveFilter;
			
			unset($inactiveFilter);
			$inactiveFilter[] = $inactiveValue;
		}
		
		if(!empty($inactiveFilter))
		{
			$q .= " AND inactive IN ('" . implode("','", $inactiveFilter) . "')"; // Convert array to MYSQL array
		}
		else
		{
			$q .= " AND inactive IN (0, 1)";
		}
		
		$q .= " ORDER BY startDate DESC";
		
		$g = $conn->prepare($q);
		$g->bindParam(1, $titleFilter);
		$g->bindParam(2, $startDateFilter);
		$g->bindParam(3, $endDateFilter);
		
		$g->execute();
		
		$bulletins = [];
		
		foreach($g->fetchAll(PDO::FETCH_COLUMN, 0) as $bulletinId)
		{
			$bulletin = new Bulletin($bulletinId);
			if($bulletin->load())
				$bulletins[] = $bulletin;
		}
		
		return $bulletins;
	}
	
	/**
	* Returns a list of Bulletin objects this user can see
	* Bulletins must be not marked as inactive, and within date range
	*/
	function getUserActiveBulletins()
	{
		global $conn;
		global $faCurrentUser;
		
		$g = $conn->prepare("SELECT id FROM Bulletin WHERE inactive = 0 AND startDate <= CURDATE() 
			AND endDate >= CURDATE() AND id IN (SELECT bulletin FROM Role_Bulletin WHERE 
			role IN (SELECT role FROM User_Role WHERE user = ?)) ORDER BY endDate DESC");
		$g->bindParam(1, $faCurrentUser->getId());
		$g->execute();
		
		$bulletins = [];
		
		foreach($g->fetchAll(PDO::FETCH_COLUMN, 0) as $bulletinId)
		{
			$bulletin = new Bulletin($bulletinId);
			
			if($bulletin->load())
				$bulletins[] = $bulletin;
		}
		
		return $bulletins;
	}