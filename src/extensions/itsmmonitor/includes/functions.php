<?php namespace itsmmonitor;

	/**
	* Get all host categories
	* @param $displayedOnly Select only displayed categories?
	*/
	function getCategories($displayedOnly = FALSE)
	{
		global $conn;
		
		$q = "SELECT id FROM ITSM_HostCategory";
		
		if($displayedOnly === TRUE)
			$q .= " WHERE displayed = 1";
		
		$q .= " ORDER BY name ASC";
		
		$g = $conn->prepare($q);
		$g->execute();
		
		$categories = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$category = new HostCategory($id);
			if($category->load())
				$categories[] = $category;
		}
		
		return $categories;
	}

