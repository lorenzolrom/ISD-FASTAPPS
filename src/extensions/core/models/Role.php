<?php
	/**
	* Representation of a Role
	*/
	class Role
	{
		private $id;
		private $name;
		
		/**
		* Constructs a Role.
		* @param id The role id, if empty is ignored
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		function getId(){return $this->id;}
		function getName(){return $this->name;}

		function setId($id){$this->id = $id;}
		function setName($name){$this->name = $name;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		/**
		* Fetches attributes from the database.
		* @return Was the fetch successful?
		*/
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT name FROM Role WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$this->name = $fetch->fetchColumn();
			
			return TRUE;
		}
		
		/**
		* Fetch attributes from the database given the name (which is unique)
		* @return Was the fetch successful?
		*/
		private function fetchFromName()
		{
			if(!isset($this->name))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM Role WHERE name = ? LIMIT 1");
			$fetch->bindParam(1, $this->name);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return TRUE;
		}
		
		/**
		* Inserts a new role into the database.
		* Sets this object's id to the id of the newly inserted role
		* @return Was the insert successful?
		*/
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO Role (name) VALUES (?)");
			$post->bindParam(1, $this->name);
			$post->execute();
			
			if($post->rowCount() == 0)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		/**
		* Updates the role with current attributes.
		* @return Was the update successful?
		*/
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE Role SET name = ? WHERE id = ?");
			$put->bindParam(1, $this->name);
			$put->bindParam(2, $this->id);
			
			// Ignores row count because updating with the same values will result in zero row count
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Deletes this role from the database.
		* Un-sets the current id.
		* @return Was the delete successful?
		*/
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			$delete = $conn->prepare("DELETE FROM Role WHERE id = ?");
			$delete->bindParam(1, $this->id);
			$delete->execute();
			
			if($delete->rowCount() == 0)
				return FALSE;
			
			unset($this->id);
			
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		private function validate($vars = [])
		{			
			$validator = new Validator();
			$errs = [];
			
			if((ifSet($vars['name']) === FALSE) OR !$validator->validLength($vars['name'], 1, 60))
			{
				$errs[] = "Name Must Be Between 1 And 60 Characters";
			}
			else
			{
				if($vars['name'] != $this->name)
				{
					$checkRoleName = new Role();
					if($checkRoleName->loadFromName($vars['name']))
						$errs[] = "Name Already In Use";
				}
			}
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function load(){return $this->fetch();}
		public function loadFromName($name = FALSE)
		{
			if($name !== FALSE)
				$this->name = $name;
			
			return $this->fetchFromName();
		}
		
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			$this->name = $vars['name'];
			
			global $conn;
			$conn->beginTransaction();
			
			if(!$this->put())
			{
				$conn->rollback();
				return FALSE;
			}
			
			// Set permissions array to blank if none have been set
			if(!isset($vars['permissions']) OR !is_array($vars['permissions']))
				$vars['permissions'] = [];
			
			// Add new permissions
			foreach($vars['permissions'] as $permission)
			{
				if(!in_array($permission, $this->getPermissions()))
				{
					if(!$this->addPermission($permission))
					{
						$conn->rollback();
						return FALSE;
					}
				}
			}
			
			// Remove old permissions
			foreach($this->getPermissions() as $permission)
			{
				if(!in_array($permission, $vars['permissions']))
				{
					if(!$this->removePermission($permission))
					{
						$conn->rollback();
						return FALSE;
					}
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			$this->name = $vars['name'];
			
			global $conn;
			$conn->beginTransaction();
			
			if(!$this->post())
			{
				$conn->rollback();
				return FALSE;
			}
			
			if(isset($vars['permissions']) AND !empty($vars['permissions']))
			{
				foreach($vars['permissions'] AS $permission)
				{
					if(!$this->addPermission($permission))
					{
						$conn->rollback();
						return FALSE;
					}
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		public function delete(){return $this->drop();}
		
		/**
		* Gets all permissions this role has
		* @return Array of permission codes
		*/
		public function getPermissions()
		{
			global $conn;
			
			$get = $conn->prepare("SELECT permission FROM Role_Permission WHERE role = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$permissions = [];
			
			foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $permission)
			{
				$permissions[] = $permission;
			}
			
			return $permissions;
		}
		
		/**
		* Adds a permission to this role
		* @param permission Permission code to add
		* @return Was the add successful?
		*/
		public function addPermission($permission)
		{
			global $conn;
			
			$add = $conn->prepare("INSERT INTO Role_Permission VALUES (?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $permission);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Removes a permission from this role
		* @param permission Permission code to remove
		* @return Was the remove successful?
		*/
		public function removePermission($permission)
		{
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM Role_Permission WHERE role = ? AND permission = ?");
			$remove->bindParam(1, $this->id);
			$remove->bindParam(2, $permission);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
	}
