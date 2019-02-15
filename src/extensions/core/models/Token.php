<?php
	/**
	* Representation of a Token
	*/
	class Token
	{
		private $token;
		private $user;
		private $issueTime;
		private $expireTime;
		private $expired;
		private $ipAddress;
		
		/**
		* Constructs a new token.
		* @param token A token. if not supplied, is ignored
		*/
		public function __construct($token = FALSE)
		{
			if($token !== FALSE)
				$this->token = $token;
		}
		
		/////
		// GET-SET
		/////
		public function getToken(){return $this->token;}
		public function getUser(){return $this->user;}
		public function getIssueTime(){return $this->issueTime;}
		public function getExpireTime(){return $this->expireTime;}
		public function getExpired(){return $this->expired;}
		public function getIpAddress(){return $this->ipAddress;}
		
		public function setUser($user){$this->user = $user;}
		
		/////
		// DATABASE FUNCTIONS
		/////
		
		/**
		* Fetches details for this token.
		* @return Did the fetch succeed?
		*/
		private function fetch()
		{
			global $conn;
			
			$fetch = $conn->prepare("SELECT user, issueTime, expireTime, expired, ipAddress FROM Token WHERE token = ? LIMIT 1");
			$fetch->bindParam(1, $this->token);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$token = $fetch->fetch();
			$this->user = $token['user'];
			$this->issueTime = $token['issueTime'];
			$this->expireTime = $token['expireTime'];
			$this->expired = $token['expired'];
			$this->ipAddress = $token['ipAddress'];
			
			return TRUE;
		}
		
		/**
		* Creates a new Token.
		* Will call a fetch to update this objects attributes.
		* @return Was the insert successful?
		*/
		private function post()
		{
			global $conn;
			
			$token_string = hash('SHA512', openssl_random_pseudo_bytes(2048));
			
			$post = $conn->prepare("INSERT INTO Token (token, user, issueTime, expireTime, ipAddress) VALUES (?, ?, NOW(), NOW() + INTERVAL 1 HOUR, ?)");
			$post->bindParam(1, $token_string); // Generates a random SHA512 token
			$post->bindParam(2, $this->user);
			$post->bindValue(3, $_SERVER['REMOTE_ADDR']); // Sets the remote ip-address of the client
			$post->execute();
			
			if($post->rowCount() == 0)
				return FALSE;
			
			$this->token = $token_string;
			$this->fetch();
			return TRUE;
		}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		public function load(){return $this->fetch();}
		public function create(){return $this->post();}
		
		/**
		* Invalidates this token.
		* @return Was the invalidation successful?
		*/
		public function invalidate()
		{
			global $conn;
			$invalidate = $conn->prepare("UPDATE Token SET expired = 1 WHERE token = ?");
			$invalidate->bindParam(1, $this->token);
			$invalidate->execute();
			
			if($invalidate->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Verifies that this token hasn't been marked as expired, and that the expire time has not passed
		* @return Is this token still valid?
		*/
		public function isValid()
		{
			if($this->expired == 1)
				return FALSE;
			
			if(strtotime($this->expireTime) < strtotime(date("Y-m-d H:i:s"))) // If time on record is less than the current time
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* Updates the expire time of the token to be 1 hour from present
		* @return Was the update successful?
		*/
		public function updateExpireTime()
		{
			global $conn;
			
			$update = $conn->prepare("UPDATE Token SET expireTime = NOW() + INTERVAL 1 HOUR WHERE token = ?");
			$update->bindParam(1, $this->token);
			$update->execute();
			
			if($update->execute())
				return TRUE;
			
			return FALSE;
		}
	}
