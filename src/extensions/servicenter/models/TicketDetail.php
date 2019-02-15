<?php namespace servicenter;

	/**
	* A detail created by a ticket update.
	* Type 'Update' - a S.D. agent's description update and time spend
	* Type 'Log' - A text log entry of what fields have been changed
	* Type 'Internal' - a S.D. agent's addition to the internal notes field
	*/
	class TicketDetail
	{
		private $id;
		private $ticket;
		private $type;
		private $user;
		private $date;
		private $data;
		private $seconds;
		
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/////
		// GET-SET
		/////
		
		public function getId(){return $this->id;}
		public function getTicket(){return $this->ticket;}
		public function getDetailType(){return $this->type;}
		public function getUser(){return $this->user;}
		public function getDetailDate(){return $this->date;}
		public function getData(){return $this->data;}
		public function getSeconds(){return $this->seconds;}
		
		public function setId($id){$this->id = $id;}
		public function setTicket($ticket){$this->ticket = $ticket;}
		public function setDetailType($type){$this->type = $type;}
		public function setUser($user){$this->user = $user;}
		public function setDetailDate($date){$this->date = $date;}
		public function setData($data){$this->data = $data;}
		public function setSeconds($seconds){$this->seconds = $seconds;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$f = $conn->prepare("SELECT ticket, `type`, user, `date`, `data`, seconds FROM ServiCenter_TicketDetail WHERE id = ? LIMIT 1");
			$f->bindParam(1, $this->id);
			$f->execute();
			
			if($f->rowCount() != 1)
				return FALSE;
			
			$d = $f->fetch();
			
			$this->ticket = $d['ticket'];
			$this->type = $d['type'];
			$this->user = $d['user'];
			$this->date = $d['date'];
			$this->data = $d['data'];
			$this->seconds = $d['seconds'];
			
			return TRUE;
		}
		
		private function post()
		{
			global $conn;
			
			$p = $conn->prepare("INSERT INTO ServiCenter_TicketDetail (ticket, type, user, `date`, `data`, seconds) VALUES 
				(?, ?, ?, NOW(), ?, ?)");
			$p->bindParam(1, $this->ticket);
			$p->bindParam(2, $this->type);
			$p->bindParam(3, $this->user);
			$p->bindParam(4, $this->data);
			$p->bindParam(5, $this->seconds);
			$p->execute();
			
			if($p->rowCount() != 1)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			return TRUE;
			
		}
		
		/////
		// BUSINESS FUNCTIONS
		////
		
		private function update($vars)
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
		}
		
		public function load(){return $this->fetch();}
		
		public function create()
		{
			return $this->post();
		}
	}
