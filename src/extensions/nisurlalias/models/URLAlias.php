<?php namespace nisurlalias;

	class URLAlias
	{
		private $id;
		private $alias;
		private $destination;
		private $disabled;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getAlias(){return $this->alias;}
		public function getDestination(){return $this->destination;}
		public function getDisabled(){return $this->disabled;}
		
		public function setId($id){$this->id = $id;}
		public function setAlias($alias){$this->alias = $alias;}
		public function setDestination($destination){$this->destination = $destination;}
		public function setDisabled($disabled){$this->disabled = $disabled;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT alias, destination, disabled FROM NIS_URLAlias WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$r = $f->fetch();
			
			$this->alias = $r['alias'];
			$this->destination = $r['destination'];
			$this->disabled = $r['disabled'];
			
			return TRUE;
		}
		
		private function fetchFromAlias()
		{
			if(!isset($this->alias))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM NIS_URLAlias WHERE alias = ? LIMIT 1");
			$f->bindParam(1, $this->alias);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO NIS_URLAlias (alias, destination, disabled) VALUES (?, ?, ?)");
			$p->bindParam(1, $this->alias);
			$p->bindParam(2, $this->destination);
			$p->bindParam(3, $this->disabled);
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
			
			$p = $conn->prepare("UPDATE NIS_URLAlias SET alias = ?, destination = ?, disabled = ? WHERE id = ?");
			$p->bindParam(1, $this->alias);
			$p->bindParam(2, $this->destination);
			$p->bindParam(3, $this->disabled);
			$p->bindParam(4, $this->id);
			
			if($p->execute())
				return TRUE;
			
			return FALSE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM NIS_URLAlias WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		private function update($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$errs = [];
			
			// Alias - is set, is no greater than 64 chars, is unique
			if(!isset($vars['alias']) OR (strlen($vars['alias']) < 1 OR strlen($vars['alias']) > 64))
				$errs[] = "Alias Must Be Between 1 And 64 Characters";
			else if($vars['alias'] != $this->alias)
			{
				// Check for existing alias
				$c = new URLAlias();
				if($c->loadFromAlias($vars['alias']))
					$errs[] = "Alias Already In Use";
			}
			
			// Destination - is set
			if(!isset($vars['destination']) OR strlen($vars['destination']) == 0)
				$errs[] = "Destination Is Required";
			
			// Disabled - is set, is 0 or 1
			if(!isset($vars['disabled']) OR !in_array($vars['disabled'], ["0", "1"]))
				$errs[] = "Disabled Value Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// SET ATTRIBUTES
			/////
			
			$this->alias = $vars['alias'];
			$this->destination = $vars['destination'];
			$this->disabled = $vars['disabled'];
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromAlias($alias = FALSE)
		{
			if($alias !== FALSE)
				$this->alias = $alias;
			
			return $this->fetchFromAlias();
		}
		
		public function create($vars)
		{
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			return $this->post();
		}
		
		public function save($vars)
		{
			$val = $this->update($vars);
			if(is_array($val))
				return $val;
			
			return $this->put();
		}
		
		public function delete()
		{
			return $this->drop();
		}
	}
