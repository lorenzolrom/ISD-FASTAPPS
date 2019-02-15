<?php
	/**
	* Utility class that will validate values against specified criteria
	*/
	class Validator
	{
		// Default constructor
		function __construct(){}
		
		/**
		* Validates if a string falls between the minimum and maximum length, inclusive
		* @return Does the string meet this criteria
		*/
		public function validLength($string, $minLength, $maxLength)
		{
			if(strlen($string) > $maxLength)
				return FALSE;
			if(strlen($string) < $minLength)
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* @return Is the supplied string greater than or longer than the minimum length?
		*/
		public function isLongEnough($string, $minLength)
		{
			return (strlen($string) >= $minLength);
		}
		
		/**
		* Validates if a string is a valid date
		* @param date The string
		* @format The date format to check against, default is ISO-8601
		*/
		public function validDate($date, $format = 'Y-m-d')
		{
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}
		
		/**
		* Determines if the supplied attribute code is valid
		* @param extension 4-Character Extension Code
		* @param type 4-Character type code
		* @param code 4-Character code
		* @return Is the code valid?
		*/
		public function isValidAttribute($extension, $type, $code)
		{
			global $conn;
			
			$check = $conn->prepare("SELECT id FROM Attribute WHERE extension = ? AND type = ? AND code = ? LIMIT 1");
			$check->bindParam(1, $extension);
			$check->bindParam(2, $type);
			$check->bindParam(3, $code);
			$check->execute();
			
			if($check->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Determines if the supplied value is contained in the supplied list
		* @param value Value to test
		* @param validValues List of values valid
		* @return Is the value valid?
		*/
		public function isValidOption($value, $validValues)
		{
			return in_array($value, $validValues);
		}
	}
