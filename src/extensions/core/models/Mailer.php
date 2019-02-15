<?php
	/**
	* E-mail sender agent
	*/
	class Mailer
	{
		private $recipients;
		private $subject;
		private $body;
		
		public function __construct($recipients = FALSE, $subject = FALSE, $body = FALSE)
		{
			if($recipients !== FALSE AND is_array($recipients)) // Recipients must be an array, if set
				$this->recipients = $recipients;
			if($subject !== FALSE)
				$this->subject = $subject;
			if($body !== FALSE)
				$this->body = $body;
		}
		
		/////
		// GET-SET
		/////
		
		public function getRecipients(){return $this->recipients;}
		public function getSubject(){return $this->subject;}
		public function getBody(){return $this->body;}
		
		/**
		* Sets the recipient list
		* @param recipients An array of User objects
		*/
		public function setRecipients($recipients)
		{
			if(is_array($recipients)) // Recipients must be array
				$this->recipients = $recipients;
		}
		public function setSubject($subject){$this->subject = $subject;}
		public function setBody($body){$this->body = $body;}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		/**
		* Send email to all users in list with a defined email address
		*/
		public function send()
		{
			if(!isset($this->recipients))
				return FALSE;
			if(!isset($this->subject))
				return FALSE;
			if(!isset($this->body))
				return FALSE;
			
			// Build e-mail message
			$headers = array(
				'From' => EMAIL_FROM_NAME . ' <' . EMAIL_FROM_ADDRESS . '>',
				'Subject' => $this->subject
			);
			
			$smtp = Mail::factory('smtp', array(
				'host' => EMAIL_HOST,
				'port' => EMAIL_PORT,
				'auth' => EMAIL_AUTH,
				'username' => EMAIL_USERNAME,
				'password' => EMAIL_PASSWORD
			));
			
			foreach($this->recipients as $recipient)
			{
				if(!filter_var($recipient->getEmail(), FILTER_VALIDATE_EMAIL)) // Skip users without a valid email address
					continue;
				$headers['To'] = $recipient->getEmail(); // Set the header 'to' address
				
				$mail = $smtp->send($recipient->getEmail(), $headers, $this->body);
			}
		}
	}
