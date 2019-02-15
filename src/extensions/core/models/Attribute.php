<?php
	/**
	* An attribute
	*/
	class Attribute 
	{
		protected $id;
		private $extension;
		protected $type;
		protected $code;
		protected $name;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getExtension(){return $this->extension;}
		public function getAttributeType(){return $this->type;}
		public function getCode(){return $this->code;}
		public function getName(){return $this->name;}
		
		public function setId($id){$this->id = $id;}
		public function setExtension($extension){$this->extension = $extension;}
		public function setAttributeType($type){$this->type = $type;}
		public function setCode($code){$this->code = $code;}
		public function setName($name){$this->name = $name;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT extension, type, code, name FROM Attribute WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$attribute = $fetch->fetch();
			
			$this->extension = $attribute['extension'];
			$this->type = $attribute['type'];
			$this->code = $attribute['code'];
			$this->name = $attribute['name'];
			
			return TRUE;
		}
		
		private function fetchFromCode()
		{
			if(!isset($this->extension) OR !isset($this->type) OR !isset($this->code))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT id FROM Attribute WHERE extension = ? AND type = ? AND code = ? LIMIT 1");
			$fetch->bindParam(1, $this->extension);
			$fetch->bindParam(2, $this->type);
			$fetch->bindParam(3, $this->code);
			$fetch->execute();
			
			if($fetch->rowCount() != 1)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE Attribute SET extension = ?, type = ?, code = ?, name = ? WHERE id = ?");
			$put->bindParam(1, $this->extension);
			$put->bindParam(2, $this->type);
			$put->bindParam(3, $this->code);
			$put->bindParam(4, $this->name);
			$put->bindParam(5, $this->id);
			
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO Attribute (extension, type, code, name) VALUES (?, ?, ?, ?)");
			$post->bindParam(1, $this->extension);
			$post->bindParam(2, $this->type);
			$post->bindParam(3, $this->code);
			$post->bindParam(4, $this->name);
			
			$post->execute();
			
			if($post->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$drop = $conn->prepare("DELETE FROM Attribute WHERE id = ?");
			$drop->bindParam(1, $this->id);
			$drop->execute();
			
			if($drop->rowCount() != 1)
				return FALSE;
			
			unset($this->id);
			
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromCode($extension = FALSE, $type = FALSE, $code = FALSE)
		{
			if($extension !== FALSE)
				$this->extension = $extension;
			if($type !== FALSE)
				$this->type = $type;
			if($code !== FALSE)
				$this->code = $code;
			
			return $this->fetchFromCode();
		}
		
		public function save()
		{
			return $this->put();
		}
		
		public function create()
		{
			return $this->post();
		}
		
		public function delete()
		{
			return $this->drop();
		}
	}
