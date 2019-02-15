<?php
	/**
	* An exception thrown when the applications runs into an error building pages
	*/
	class AppException extends Exception
	{
		protected $customCode;
		
		/**
		* Override Exception constructor, code is allowed to be String
		*/
		public function __construct($message, $customCode, $code = 0)
		{
			parent::__construct($message, $code);
			
			$this->customCode = $customCode;
		}
		
		public function getCustomCode()
		{
			return $this->customCode;
		}
	}
