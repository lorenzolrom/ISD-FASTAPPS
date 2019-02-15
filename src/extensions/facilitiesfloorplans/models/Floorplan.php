<?php namespace facilitiesfloorplans;
	use facilitiescore as fc;
	
	/**
	* A floorplan in a Building
	*/
	class Floorplan
	{
		private $id;
		private $building;
		private $floor;
		private $imagePath;
		private $createUser;
		private $createDate;
		private $modifyUser;
		private $modifyDate;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getBuilding(){return $this->building;}
		public function getFloor(){return $this->floor;}
		public function getImagePath(){return $this->imagePath;}
		public function getCreateUser(){return $this->createUser;}
		public function getCreateDate(){return $this->createDate;}
		public function getModifyUser(){return $this->modifyUser;}
		public function getModifyDate(){return $this->modifyDate;}
		
		public function setId($id){$this->id = $id;}
		public function setBuilding($building){$this->building = $building;}
		public function setFloor($floor){$this->floor = $floor;}
		public function setImagePath($imagePath){$this->imagePath = $imagePath;}
		
		/////
		// DATABASE
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT building, `floor`, imagePath, createUser, 
				createDate, modifyUser, modifyDate FROM Facilities_Floorplan 
				WHERE id = ? LIMIT 1");
				
			$g->bindParam(1, $this->id);
			$g->execute();
			
			if($g->rowCount() != 1)
				return FALSE;
			
			$r = $g->fetch();
			
			$this->building = $r['building'];
			$this->floor = $r['floor'];
			$this->imagePath = $r['imagePath'];
			$this->createUser = $r['createUser'];
			$this->createDate = $r['createDate'];
			$this->modifyUser = $r['modifyUser'];
			$this->modifyDate = $r['modifyDate'];
			
			return TRUE;
		}
		
		private function fetchFromBuildingFloor()
		{
			if(!isset($this->building) OR !isset($this->floor))
				return FALSE;
			
			global $conn;
			
			$g = $conn->prepare("SELECT id FROM Facilities_Floorplan WHERE 
				building = ? AND `floor` = ? LIMIT 1");
			$g->bindParam(1, $this->building);
			$g->bindParam(2, $this->floor);
			$g->execute();
			
			if($g->rowCount() != 1)
				return FALSE;
			
			$this->id = $g->fetchColumn();
			
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO Facilities_Floorplan (building, 
				`floor`, imagePath, createUser, createDate, modifyUser, modifyDate) 
				VALUES (?, ?, ?, ?, NOW(), ?, NOW())");
				
			$p->bindParam(1, $this->building);
			$p->bindParam(2, $this->floor);
			$p->bindParam(3, $this->imagePath);
			$p->bindParam(4, $this->createUser);
			$p->bindParam(5, $this->createUser);
			
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$p = $conn->prepare("UPDATE Facilities_Floorplan SET building = ?, 
				`floor` = ?, imagePath = ?, modifyUser = ?, modifyDate = NOW() 
				WHERE id = ?");
				
			$p->bindParam(1, $this->building);
			$p->bindParam(2, $this->floor);
			$p->bindParam(3, $this->imagePath);
			$p->bindParam(4, $this->modifyUser);
			$p->bindParam(5, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM Facilities_Floorplan WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS
		/////
		
		private function update($vars = [], $files = [])
		{
			global $faCurrentUser;
			
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$errs = [];
			$building = NULL;
			
			// Building Code - is set, is valid
			if(!isset($vars['buildingCode']))
				$errs[] = "Building Code Required";
			else
			{
				$building = new fc\Building();
				if(!$building->loadFromCode($vars['buildingCode']))
					$errs[] = "Building Code Not Found";
			}
			
			// Floor - is set, not greater than 64 characters, is not already in this building
			if(!isset($vars['floor']) OR (strlen($vars['floor']) < 1 OR strlen($vars['floor']) > 64))
				$errs[] = "Floor Must Be Between 1 And 64 Characters";
			else if($building !== NULL AND $vars['floor'] != $this->floor)
			{
				$check = new Floorplan();
				if($check->loadFromBuildingFloor($building->getId(), $vars['floor']))
					$errs[] = "Floor Already In Use For Specified Building";
			}
			
			// File - Is set (if current file is empty), is unique
			
			if(strlen($this->imagePath) == 0 AND empty($files['image']['name']))
				$errs[] = "Floorplan Image Not Set";
			else if((basename($files['image']['name']) != $this->imagePath) AND (basename($files['image']['name']) != "") AND file_exists(FACILITIES_FLOORPLANS_IMAGEPATH . basename($files['image']['name'])))
				$errs[] = "Floorplan Image Name Already Exists";
			else if(!empty($files['image']['name']) AND !in_array(strtolower(pathinfo(basename($files['image']['name']), PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'gif']))
				$errs[] = "Floorplan Image Can Only Be Of Type .png, .jpg, .jpeg, AND .gif";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET ATTRIBUTES
			/////
			
			$this->building = $building->getId();
			$this->floor = $vars['floor'];
			$this->modifyUser = $faCurrentUser->getId();
			$this->modifyDate = date('Y-m-d');
			
			/////
			// UPLOAD FILE (IF SUPPLIED)
			/////
			
			if(!empty(basename($files['image']['name'])) AND basename($files['image']['name']) != $this->imagePath)
			{
				// Delete existing image
				if(strlen($this->imagePath) != 0)
					unlink(FACILITIES_FLOORPLANS_IMAGEPATH . $this->imagePath);
				
				$filename = basename($files['image']['name']);
				$filepath = FACILITIES_FLOORPLANS_IMAGEPATH . $filename;
				
				if(move_uploaded_file($files['image']['tmp_name'], $filepath))
					$this->imagePath = $filename;
				else
					return FALSE;
			}
			
			return TRUE;
		}
		
		public function load(){return $this->fetch();}
		
		public function loadFromBuildingFloor($building = FALSE, $floor = FALSE)
		{
			if($building !== FALSE)
				$this->building = $building;
			if($floor !== FALSE)
				$this->floor = $floor;
			
			return $this->fetchFromBuildingFloor();
		}
		
		public function create($vars, $files)
		{
			global $faCurrentUser;
			
			// Set create use as currently logged in user
			$this->createUser = $faCurrentUser->getId();
			
			$val = $this->update($vars, $files);
			if(is_array($val))
				return $val;
			if($val === FALSE)
				return FALSE;
			
			return $this->post();
		}
		
		public function save($vars, $files)
		{
			$val = $this->update($vars, $files);
			if(is_array($val))
				return $val;
			if($val === FALSE)
				return FALSE;
			
			return $this->put();
		}
		
		public function delete()
		{
			// Delete image
			if(!unlink(FACILITIES_FLOORPLANS_IMAGEPATH . $this->imagePath))
				return FALSE;
			
			return $this->drop();
		}
	}
	
