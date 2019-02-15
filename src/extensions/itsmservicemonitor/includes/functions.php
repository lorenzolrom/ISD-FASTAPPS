<?php namespace itsmservicemonitor;

	/**
	* Get all application categories
	* @param $displayedOnly Select only displayed categories?
	*/
	function getCategories($displayedOnly = FALSE)
	{
		global $conn;
		
		$q = "SELECT id FROM ITSM_ApplicationCategory";
		
		if($displayedOnly === TRUE)
			$q .= " WHERE displayed = 1";
		
		$q .= " ORDER BY name ASC";
		
		$g = $conn->prepare($q);
		$g->execute();
		
		$categories = [];
		
		foreach($g->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$category = new ApplicationCategory($id);
			if($category->load())
				$categories[] = $category;
		}
		
		return $categories;
	}

