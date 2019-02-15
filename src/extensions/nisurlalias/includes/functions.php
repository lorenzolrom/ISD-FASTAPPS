<?php namespace nisurlalias;

	/**
	* Returns URL Aliases
	*/
	function getAliases($aliasFilter = "%", $destinationFilter = "%")
	{
		global $conn;
		
		$q = "SELECT id FROM NIS_URLAlias WHERE alias LIKE ? AND destination LIKE ?";
		
		$q .= " ORDER BY alias ASC";
		
		$get = $conn->prepare($q);
		$get->bindParam(1, $aliasFilter);
		$get->bindParam(2, $destinationFilter);
		$get->execute();
		
		$aliases = [];
		
		foreach($get->fetchAll(\PDO::FETCH_COLUMN, 0) as $id)
		{
			$alias = new URLAlias($id);
			if($alias->load())
				$aliases[] = $alias;
		}
		
		return $aliases;
	}

