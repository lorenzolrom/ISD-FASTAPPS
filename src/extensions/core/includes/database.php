<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// Database Abstraction Classes & Connection Initialization
	////////////////////
	
	/**
	* Intermediate layer for database connectivity
	*/
	class DatabaseConnection
	{
		private $conn;
		
		/**
		* Construct new database connection
		* @param host Hostname for server
		* @param database Database name
		* @param user Username with access to database
		* @param password Above user's password
		* @param options Array of options for database connection, default is empty
		*/
		public function __construct($host, $database, $user, $password, $options = [])
		{
			try
			{
				$this->conn = new PDO("mysql:host=" . $host . ";dbname=" . $database, $user, $password, $options);
			}
			catch(PDOException $e)
			{
				die("Database Connection Failed");
			}
		}
		
		/**
		* Directly query the database
		* @return Array of query results
		*/
		public function query($query)
		{
			try
			{
				return $this->conn->query($query);
			}
			catch(PDOException $e)
			{
				throw new AppException("Query Failure", "D06");
			}
		}
		
		/**
		* Create a new prepared statement
		* @param query Query string
		* @return DatabasePreparedStatement object
		*/
		public function prepare($query)
		{
			return new DatabasePreparedStatement($this->conn, $query);
		}
		
		/**
		* @return Last insert ID for this database connection
		*/
		public function lastInsertId()
		{
			return $this->conn->lastInsertId();
		}
		
		/**
		* Start transaction
		*/
		public function beginTransaction()
		{
			try
			{
				$this->conn->beginTransaction();
			}
			catch(PDOException $e)
			{
				throw new AppException("Failed To Begin Transaction", "D07");
			}
		}
		
		/**
		* Rollback transaction
		*/
		public function rollBack()
		{
			try
			{
				$this->conn->rollBack();
			}
			catch(PDOException $e)
			{
				throw new AppException("Failed To Roll-Back Transaction", "D07");
			}
		}
		
		/**
		* Commit transaction
		*/
		public function commit()
		{
			try
			{
				$this->conn->commit();
			}
			catch(PDOException $e)
			{
				throw new AppException("Failed To Commit Transaction", "D07");
			}
		}
		
		/**
		* De-reference the PDO connection
		*/
		public function close()
		{
			$this->conn = NULL;
		}
	}
	
	/**
	* Intermediate layer for prepared statements
	*/
	class DatabasePreparedStatement
	{
		private $statement;
		
		/**
		* Construct new Prepared Statement
		* @param conn Database connection object
		* @param query Query string
		*/
		public function __construct($conn, $query)
		{
			$this->statement = $conn->prepare($query);
		}
		
		/**
		* Bind parameter to prepared statement
		* @param index Index for parameter, either string or integer
		* @param value A referenced value
		*/
		public function bindParam($index, $value)
		{
			$this->statement->bindParam($index, $value);
		}
		
		/**
		* Bind value to prepared statement
		* @param index Index for parameter, either string or integer
		* @param value Value to bind
		*/
		public function bindValue($index, $value)
		{
			$this->statement->bindValue($index, $value);
		}
		
		/**
		* Execute prepared statement
		* @return True if query was successful?
		*/
		public function execute()
		{
			try
			{
				$this->statement->execute();
				return TRUE;
			}
			catch(PDOException $e)
			{
				if($e->getCode() == 23000) // Catch constraint exceptions
				{
					$rawMessage = $e->getMessage();
					$values = explode('`', $e->getMessage());
					
					if(isset($values[3]) AND isset($values[9]))
						throw new AppException($values[9] . " Is Referenced By A(n) " . $values[3], "D10");
				}
				
				throw new AppException("Query Failure " . $e->getMessage(), "D08");
			}
		}
		
		/**
		* @return Next row in results
		*/
		public function fetch()
		{
			return $this->statement->fetch();
		}
		
		/**
		* Returns array of results from query
		* @param fetchType Option for how data should be returned
		* @param fetchArgument Arguments for above option
		* @return Array of values
		*/
		public function fetchAll($fetchType = FALSE, $fetchArgument = 0)
		{
			if($fetchType !== FALSE)
				return $this->statement->fetchAll($fetchType, $fetchArgument);
			else
				return $this->statement->fetchAll();
		}
		
		/**
		* @return Next column in results
		*/
		public function fetchColumn()
		{
			return $this->statement->fetchColumn();
		}
		
		/**
		* Number of rows in query results
		*/
		public function rowCount()
		{
			return $this->statement->rowCount();
		}
	}
	
	$conn = new DatabaseConnection(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
