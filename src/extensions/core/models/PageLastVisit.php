<?php
	/**
	* A record of a user's last visit to a page
	*/
	class PageLastVisit
	{
		private $loaded;
		private $user;
		private $page;
		private $getVars;
		
		public function __construct($user, $page)
		{
			$this->user = $user;
			$this->page = $page;
			$this->loaded = FALSE; // Not loaded from DB
			
			$this->loaded = $this->fetch(); // Attempt to load record
		}
		
		/////
		// GET-SET
		/////
		public function setGetVars($getVars)
		{
			$getString = "";
			
			if(!empty($getVars)) // If GET array is not empty
			{
				$getString = "?"; // Start
				
				foreach(array_keys($getVars) as $key) // Building string
				{
					if($key == "NOTICE") // Skip System Notifications
						continue;
					
					if(!is_array($getVars[$key]))
						$getString .= $key . "="  . $getVars[$key] . "&";
					else
					{
						foreach($getVars[$key] as $value)
						{
							$getString .= $key . "%5B%5D=" . $value . "&";
						}
					}
				}
				
				$getString = rtrim($getString, "&"); // Remove trailing '&'
			}
			
			$this->getVars = $getString; // Assign to this object
		}
		
		public function getGetVars()
		{
			return $this->getVars; // Return get variables
		}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			global $conn;
			$fetch = $conn->prepare("SELECT getVars FROM PageLastVisit WHERE user = ? AND page = ? LIMIT 1");
			$fetch->bindParam(1, $this->user);
			$fetch->bindParam(2, $this->page);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$pageLastVisit = $fetch->fetch();
			
			$this->getVars = $pageLastVisit['getVars'];			
			return TRUE;
		}
		
		private function post()
		{
			global $conn;
			$post = $conn->prepare("INSERT INTO PageLastVisit (user, page, getVars) VALUES (?, ?, ?)");
			$post->bindParam(1, $this->user);
			$post->bindParam(2, $this->page);
			$post->bindParam(3, $this->getVars);
			$post->execute();
			
			if($post->rowCount() == 1)
			{
				$this->loaded = TRUE;
				return TRUE;
			}
			
			return FALSE;
		}
		
		private function put()
		{
			if(!$this->loaded)
				return FALSE;
			
			global $conn;
			$put = $conn->prepare("UPDATE PageLastVisit SET getVars = ? WHERE user = ? and page = ?");
			$put->bindParam(1, $this->getVars);
			$put->bindParam(2, $this->user);
			$put->bindParam(3, $this->page);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!$this->loaded)
				return FALSE;
			
			global $conn;
			$drop = $conn->prepare("DELETE FROM PageLastVisit WHERE user = ? AND page = ?");
			$drop->bindParam(1, $this->user);
			$drop->bindParam(2, $this->page);
			$drop->execute();
			
			if($drop->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function delete()
		{
			return $this->drop();
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		/**
		* Updates existing record if present,
		* creates new record if not
		*/
		public function save()
		{
			if($this->loaded) // Was the record pulled?
			{
				// Update record
				return $this->put();
			}
			else // Record does not exist
			{
				// Create new record
				return $this->post();
			}
		}
	}
