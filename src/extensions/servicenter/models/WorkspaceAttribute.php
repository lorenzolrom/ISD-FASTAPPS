<?php namespace servicenter;

	/**
	* A ticket attribute specific to a workspace
	*/
	class WorkspaceAttribute extends \Attribute
	{
		private $workspace;
		private $default;
		
		/////
		// GET-SET
		/////
		
		public function getWorkspace(){return $this->workspace;}
		public function getDefault(){return $this->default;}
		
		public function setWorkspace($workspace){$this->workspace = $workspace;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT workspace, type, code, name, `default` FROM ServiCenter_TicketAttribute WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$a = $f->fetch();
			
			$this->workspace = $a['workspace'];
			$this->type = $a['type'];
			$this->code = $a['code'];
			$this->name = $a['name'];
			$this->default = $a['default'];
			
			return TRUE;
		}
		
		private function fetchFromCode()
		{
			if(!isset($this->workspace) OR !isset($this->type) OR !isset($this->code))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WBERE workspace = ? AND type = ? AND code = ? LIMIT 1");
			$f->bindParam(1, $this->workspace);
			$f->bindParam(2, $this->type);
			$f->bindParam(3, $this->code);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			return $this->fetch();
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ServiCenter_TicketAttribute (workspace, type, code, name, `default`) VALUES (?, ?, ?, ?, ?)");
			$p->bindParam(1, $this->workspace);
			$p->bindParam(2, $this->type);
			$p->bindParam(3, $this->code);
			$p->bindParam(4, $this->name);
			$p->bindParam(5, $this->default);
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		private function drop()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$d = $conn->prepare("DELETE FROM ServiCenter_TicketAttribute WHERE id = ?");
			$d->bindParam(1, $this->id);
			$d->execute();
			
			if($d->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		
		public function loadFromCode($workspace = FALSE, $type = FALSE, $code = FALSE)
		{
			if($workspace !== FALSE)
				$this->workspace = $workspace;
			if($type !== FALSE)
				$this->type = $type;
			if($code !== FALSE)
				$this->code = $code;
			
			if(!isset($this->workspace) OR !isset($this->type) OR !isset($this->code))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WHERE workspace = ? AND type = ? AND code = ? LIMIT 1");
			$f->bindParam(1, $this->workspace);
			$f->bindParam(2, $this->type);
			$f->bindParam(3, $this->code);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$this->id = $f->fetchColumn();
			return $this->fetch();
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			global $conn;
			
			$validator = new \Validator();
			
			$errs = [];
			
			if(!isset($vars['code']) OR strlen($vars['code']) != 4)
				$errs[] = "Code Must Be 4 Characters";
			else if(!ctype_alnum($vars['code']))
				$errs[] = "Cost Must Only Contain Letters And Numbers";
			
			if(!isset($vars['name']) OR !$validator->validLength($vars['name'], 1, 30))
				$errs[] = "Name Must Be Between 1 And 30 Characters";
			
			// Default - is set, is 0 or 1
			if(!isset($vars['default']) OR !in_array($vars['default'], [0, 1]))
				$errs[] = "Default Value Is Invalid";
			
			// Verify code does not already exist
			$v = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WHERE workspace = ? AND type = ? AND code = ? LIMIT 1");
			$v->bindParam(1, $this->workspace);
			$v->bindParam(2, $this->type);
			$v->bindParam(3, $vars['code']);
			$v->execute();
			
			if($v->rowCount() == 1)
				$errs[] = "Code Already Exists In This Workspace";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			$this->code = strip_tags(htmlentities($vars['code']));
			$this->name = strip_tags(htmlentities($vars['name']));
			
			// If there is no default attribute, set this one
			$v = $conn->prepare("SELECT id FROM ServiCenter_TicketAttribute WHERE workspace = ? AND type = ? AND `default` = 1 LIMIT 1");
			$v->bindParam(1, $this->workspace);
			$v->bindParam(2, $this->type);
			$v->execute();
			if($v->rowCount() == 1)
				$this->default = 0;
			else
				$this->default = 1;
			
			$v = $conn->prepare("SELECT id FROM Attribute WHERE extension='srvc' AND type = ? AND code = ?");
			$v->bindParam(1, $this->type);
			$v->bindParam(2, $this->code);
			$v->execute();
			
			if($v->rowCount() == 1)
				return FALSE;
			
			if($this->post())
			{
				if($vars['default'] == 1)
					$this->setDefault();
				return TRUE;
			}
			return FALSE;
		}
		
		public function delete()
		{			
			return $this->drop();
		}
		
		public function setDefault()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			// Remove all other default attributes
			$r = $conn->prepare("UPDATE ServiCenter_TicketAttribute SET `default` = 0 WHERE workspace = ? AND type = ?");
			$r->bindParam(1, $this->workspace);
			$r->bindParam(2, $this->type);
			$r->execute();
			
			// Set this to default
			$s = $conn->prepare("UPDATE ServiCenter_TicketAttribute SET `default` = 1 WHERE id = ?");
			$s->bindParam(1, $this->id);
			$s->execute();
			
			if($s->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
	}
