<?php
	/**
	* Representation of a Page
	*/
	class Page
	{
		private $id;
		private $title;
		private $url;
		private $permission;
		private $type;
		private $parent;
		private $weight;
		private $icon;
		private $extension;
		
		/**
		* Constructs a new Page
		* @param id The id of the page.  If empty will be ignored.
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		function getID(){return $this->id;}
		function getTitle(){return $this->title;}
		function getUrl(){return $this->url;}
		function getPermission(){return $this->permission;}
		function getPageType(){return $this->type;}
		function getParent(){return $this->parent;}
		function getWeight(){return $this->weight;}
		function getIcon(){return $this->icon;}
		function getExtension(){return $this->extension;}
		
		function setUrl($url){$this->url = $url;}
		function setWeight($weight){$this->weight = $weight;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		/**
		* Fetches attributes from database
		* @return Was the fetch successful?
		*/
		private function fetch()
		{
			global $conn;
			$fetch = $conn->prepare("SELECT title, url, permission, type, parent, weight, icon, extension FROM Page WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$page = $fetch->fetch();
			
			$this->title = $page['title'];
			$this->url = $page['url'];
			$this->permission = $page['permission'];
			$this->type = $page['type'];
			$this->parent = $page['parent'];
			$this->weight = $page['weight'];
			$this->icon = $page['icon'];
			$this->extension = $page['extension'];
			
			return TRUE;
		}
		
		/**
		* Fetches attributes from database based off of currently set URL
		* Will set this object's id and then call fetch()
		* @return Was the fetch successful?
		*/
		private function fetchFromUrl()
		{
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM Page WHERE url = ? LIMIT 1");
			$fetch->bindParam(1, $this->url);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return($this->fetch());
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		public function loadFromURL($url = FALSE)
		{
			if($url !== FALSE)
				$this->url = $url;
			
			return $this->fetchFromUrl();
		}
		
		/**
		* Returns a list of child pages for this page
		* @param useWeight should pages be sorted by weight
		* @return Array of Page objects
		*/
		public function getChildren($useWeight = FALSE)
		{
			global $conn;
			
			$query = "SELECT id FROM Page WHERE parent = ?";
			
			if($useWeight)
			{
				$query .= " ORDER BY weight ASC";
			}
			
			$get = $conn->prepare($query);
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$children = [];
			
			foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $page_id)
			{
				$page = new Page($page_id);
				if($page->fetch())
					$children[] = $page;
			}
			
			return $children;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		/**
		* Returns an array of Pages representing the parent stack
		* @return Array of Page objects
		* Deepest page is first
		*/
		public function getParentStack()
		{
			// If the parent page is set
			if(strlen($this->parent) !== NULL)
			{
				$parentPage = new Page($this->parent);
				
				if($parentPage->fetch())
				{
					return array_merge($parentPage->getParentStack(), [$this]);
				}
			}
			
			return [$this];
		}
		
		/**
		* Returns the last GET variables used on this page as a string
		* or FALSE if there are none
		*/
		public function getLastGetVars()
		{
			global $faCurrentUser;
			global $_GET;
			
			$plv = new PageLastVisit($faCurrentUser->getId(), $this->id);
			
			if($plv->load()) // Record exists
			{
				// Compare current get vars and set get vars
				return $plv->getGetVars();
			}
			
			return FALSE;
		}
		
		public function setLastGetVars()
		{
			global $faCurrentUser;
			global $_GET;
			
			$plv = new PageLastVisit($faCurrentUser->getId(), $this->id);			
			$plv->setGetVars($_GET);
			$plv->save();
		}
	}
