<?php
	/**
	* Representation of a user
	*/
	class User
	{
		private $id;
		private $username;
		private $firstName;
		private $lastName;
		private $email;
		private $password;
		private $disabled;
		private $authType;
		
		/**
		* Constructs a new user.
		* @param id The id of the user.  If not supplied, it is ignored
		*/
		public function __construct($id = FALSE)
		{
			if($id !== FALSE)
				$this->id = $id;
		}
		
		/**
		* Fetches user attributes from database
		* If the user's id has been set.
		* @return Was the fetch successful?
		*/
		private function fetch()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$fetch = $conn->prepare("SELECT username, firstName, lastName, email, disabled, password, authType FROM User WHERE id = ? LIMIT 1");
			$fetch->bindParam(1, $this->id);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$user = $fetch->fetch();
			
			$this->username = $user['username'];
			$this->firstName = $user['firstName'];
			$this->lastName = $user['lastName'];
			$this->email = $user['email'];
			$this->disabled = $user['disabled'];
			$this->password = $user['password'];
			$this->authType = $user['authType'];
			
			return TRUE;
		}
		
		/**
		* Fetches user attributes from databse using username only.
		* @return Was the fetch successful?
		*/
		private function fetchFromUsername()
		{
			if(!isset($this->username))
				return FALSE;
			
			global $conn;
			$fetch = $conn->prepare("SELECT id FROM User WHERE username = ? LIMIT 1");
			$fetch->bindParam(1, $this->username);
			$fetch->execute();
			
			if($fetch->rowCount() == 0)
				return FALSE;
			
			$this->id = $fetch->fetchColumn();
			
			return $this->fetch();
		}
		
		/**
		* Creates a new user in the database using existing attributes.
		* Will set this object's id to the id of the newly inserted user.
		* @return Was the insert successful?
		*/
		private function post()
		{
			global $conn;
			
			$post = $conn->prepare("INSERT INTO User (username, firstName, lastName, email, password, disabled, authType) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$post->bindParam(1, $this->username);
			$post->bindParam(2, $this->firstName);
			$post->bindParam(3, $this->lastName);
			$post->bindParam(4, $this->email);
			$post->bindParam(5, $this->password);
			$post->bindParam(6, $this->disabled);
			$post->bindParam(7, $this->authType);
			$post->execute();
			
			if($post->rowCount() == 0)
				return FALSE;
			
			$this->id = $conn->lastInsertId();
			
			return TRUE;
		}
		
		private function put()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			
			$put = $conn->prepare("UPDATE User SET username = ?, firstName = ?, lastName = ?, email=?, password = ?, disabled = ?, authType = ? WHERE id = ?");
			$put->bindParam(1, $this->username);
			$put->bindParam(2, $this->firstName);
			$put->bindParam(3, $this->lastName);
			$put->bindParam(4, $this->email);
			$put->bindParam(5, $this->password);
			$put->bindParam(6, $this->disabled);
			$put->bindParam(7, $this->authType);
			$put->bindParam(8, $this->id);
			
			// Ignores row count because updating a row without changing information will result in a zero-row count
			if($put->execute())
				return TRUE;
			
			return FALSE;
		}
		
		/////
		// GET-SET
		/////
		public function getId(){return $this->id;}
		public function getUsername(){return $this->username;}		
		public function getFirstName(){return $this->firstName;}
		public function getlastName(){return $this->lastName;}
		public function getEmail(){return $this->email;}
		public function getDisabled(){return $this->disabled;}
		public function getAuthType(){return $this->authType;}
		
		public function setId($id){$this->id = $id;}
		public function setUsername($username){$this->username = $username;}
		public function setFirstName($firstName){$this->firstName = $firstName;}
		public function setlastName($lastName){$this->lastName = $lastName;}
		public function setEmail($email){$this->email = $email;}
		public function setDisabled($disabled){$this->disabled = $disabled;}
		public function setAuthType($authType){$this->authType = $authType;}
		
		/**
		* Sets the user's password
		* @param password The new password, will be hashed using SHA512 prior to update
		*/
		public function setPassword($password){$this->password = hash('SHA512', $password);}
		
		/////
		// BUSINESS FUNCTIONS
		/////
		
		/**
		* Validates if the supplied username exists in LDAP
		* @return FALSE if user not found, array of attributes if user was found
		*/
		private function validateLDAPUsername($username)
		{
			$ldap = new LDAPConnection(LDAP_DOMAIN_CONTROLLER, LDAP_DOMAIN, LDAP_DOMAIN_DN);
			$ldap->startTLS();
			
			if(!$ldap->bind(LDAP_USERNAME, LDAP_PASSWORD))
				throw new AppException("Failed To Bind LDAP Session", "L04");
			
			// Validate username
			$ldapAttributes = array("givenName", "sn", "mail");
			$ldapResults = $ldap->searchByUsername($username, $ldapAttributes);
			
			if($ldapResults['count'] == 1)
			{
				$ldapUser = $ldapResults[0];
				$ldapDetails = [];
				
				// Set variables
				if(isset($ldapUser['givenname'][0]))
					$this->firstName = $ldapUser['givenname'][0];
				if(isset($ldapUser['sn'][0]))
					$this->lastName = $ldapUser['sn'][0];
				if(isset($ldapUser['mail'][0]))
					$this->email = $ldapUser['mail'][0];
				
				return $ldapDetails;
			}
			
			return FALSE;
		}
		
		/**
		* Validate attributes for a local user
		* @param $checkPasswordOnlyIfSet If this is set, password is only checked if a new one has been set
		*/
		private function validateLocalUser($vars = [], $checkPasswordOnlyIfSet = FALSE)
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$errs = [];
			
			$validator = new Validator();
			
			// Name
			if((ifSet($vars['firstName']) === FALSE) OR !$validator->validLength($vars['firstName'], 1, 30))
			{
				$errs[] = "First Name Must Be Between 1 And 30 Characters";
			}
			else if(!preg_match("/^[A-Za-z0-9-\.]+$/", $vars['firstName']))
				$errs[] = "First Name May Only Contain Letters, Numbers, '-', And '.'";
			
			if((ifSet($vars['lastName']) === FALSE) OR !$validator->validLength($vars['lastName'], 1, 30))
			{
				$errs[] = "Last Name Must Be Between 1 And 30 Characters";
			}
			else if(!preg_match("/^[A-Za-z0-9-\.]+$/", $vars['lastName']))
				$errs[] = "Last Name May Only Contain Letters, Numbers, '-', And '.'";

			// Email
			if(!empty($vars['email']) AND !filter_var($vars['email'], FILTER_VALIDATE_EMAIL))
				$errs[] = "Email Is Invalid";

			// Password
			if(((ifSet($vars['password']) === FALSE) OR !$validator->isLongEnough($vars['password'], 8)) AND $checkPasswordOnlyIfSet === FALSE)
			{
				$errs[] = "Password Must Be 8 Characters Or Longer";
			}
			else if((ifSet($vars['confirm']) === FALSE) OR $vars['password'] != $vars['confirm'])
			{
				$errs[] = "Password And Confirm Password Must Match";
			}
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		/**
		* Common validator for all users (local and LDAP)
		*/
		private function validate($vars = [], $checkPasswordOnlyIfSet = FALSE)
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$validator = new Validator();
			
			$errs = [];
			
			// Username
			if((!isset($vars['username'])) OR !$validator->validLength($vars['username'], 1, 64))
				$errs[] = "Username Must Be Between 1 And 64 Characters";
			else if(!ctype_alnum($vars['username']))
				$errs[] = "Username Must Only Consist Of Letters And Numbers";
			else if(!preg_match("/^[A-Za-z0-9-.]+$/", $vars['username']))
				$errs[] = "Username Must Only Consist Of Letters, Numbers, And '-'";
			else if($this->username != $vars['username']) // Username already exists
			{
				// Check if username exists
				$checkUser = new User();
				$checkUser->setUsername($vars['username']);
				if($checkUser->loadFromUsername())
					$errs[] = "Username Already In Use";
			}
			
			// Account disabled
			if(!($vars['disabled'] == 0 OR $vars['disabled'] == 1))
			{
				$errs[] = "Status Is Invalid";
			}
			
			if($vars['authType'] == "loca")
			{
				// Validate local user
				$local = $this->validateLocalUser($vars, $checkPasswordOnlyIfSet);
				
				if(is_array($local))
					$errs = array_merge($errs, $local);
			}
			else if($vars['authType'] == "ldap" AND LDAP_ENABLED === TRUE)
			{
				if(empty($errs))
				{
					$ldap = $this->validateLDAPUsername($vars['username']);
					
					if($ldap === FALSE)
						$errs[] = "LDAP User Not Found";
				}
			}
			else
				$errs[] = "Authentication Type Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			return TRUE;
		}
		
		public function load()
		{
			return $this->fetch();
		}
		
		public function loadFromUsername($username = FALSE)
		{
			if($username !== FALSE)
				$this->username = $username;
			
			return $this->fetchFromUsername();
		}
		
		public function create($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars);
			
			if(is_array($val))
				return $val;
			
			$this->username = $vars['username'];
			$this->disabled = $vars['disabled'];
			$this->authType = $vars['authType'];
			$this->setPassword($vars['password']);
			
			if(!isset($this->firstName))
				$this->firstName = $vars['firstName'];
			if(!isset($this->lastName))
				$this->lastName = $vars['lastName'];
			if(!isset($this->email) AND isset($vars['email']))
				$this->email = $vars['email'];
			else if(!isset($this->email) AND !isset($vars['email']))
				$this->email = "";
			
			global $conn;
			
			$conn->beginTransaction();
			
			if($this->post() === FALSE)
			{
				$conn->rollBack();
				return FALSE;
			}
			
			if(!isset($vars['roles']) OR !is_array($vars['roles']))
				$vars['roles'] = [];
			
			foreach($vars['roles'] as $id)
			{
				if(!$this->addRole($id))
				{
					$conn->rollBack();
					return FALSE;
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		public function save($vars = [])
		{
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			$val = $this->validate($vars, TRUE);
			
			if(is_array($val))
				return $val;
			
			if($vars['authType'] == "loca" AND strlen($this->password) == 0 AND strlen($vars['password']) == 0)
				return ["Password Has Not Been Set"];
			
			$this->username = $vars['username'];
			$this->disabled = $vars['disabled'];
			
			$this->authType = $vars['authType'];
			
			if(isset($vars['email']) AND strlen($vars['email']) == 0 AND strlen($this->email) == 0)
				$this->email = "";
			
			if(isset($vars['firstName']) AND strlen($vars['firstName']) != 0)
				$this->firstName = $vars['firstName'];
			if(isset($vars['lastName']) AND strlen($vars['lastName']) != 0)
				$this->lastName = $vars['lastName'];
			
			if(isset($vars['password']) AND !empty($vars['password']))
				$this->setPassword($vars['password']);
			
			global $conn;
			
			$conn->beginTransaction();
			
			if($this->put() === FALSE)
			{
				$conn->rollBack();
				return FALSE;
			}
			
			if(!isset($vars['roles']) OR !is_array($vars['roles']))
				$vars['roles'] = [];
			
			// Get current role IDs
			$roleIds = [];
			foreach($this->getRoles() as $role)
			{
				$roleIds[] = $role->getId();
			}
			
			// Add new roles
			foreach($vars['roles'] as $id)
			{
				if(!in_array($id, $roleIds))
				{
					if(!$this->addRole($id))
					{
						$conn->rollBack();
						return FALSE;
					}
				}
			}
			
			// Delete old roles
			foreach($roleIds as $id)
			{
				if(!in_array($id, $vars['roles']))
				{
					if(!$this->removeRole($id))
					{
						$conn->rollBack();
						return FALSE;
					}
				}
			}
			
			$conn->commit();
			return TRUE;
		}
		
		// Performs user logout operations
		public function logout()
		{
			$this->deletePageLastVisitRecords();
		}
		
		// Performs user login operations
		public function login()
		{
			$this->invalidateTokens();
			// If user is LDAP
			if($this->authType == "ldap")
				$this->refreshLDAPDetails();
		}
		
		/**
		* Validates if the supplied password is correct for this user.
		* @param password The user password (in plain text)
		* @return Is the password correct?
		*/
		public function isCorrectPassword($password)
		{
			if($this->password == hash('SHA512', $password))
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* @return Does this user have a password?
		*/
		public function hasPassword()
		{
			if(empty($this->password))
				return FALSE;
			
			return TRUE;
		}
		
		/**
		* Returns an array of roles this user is a member of
		* @return Array of Role objects
		*/
		public function getRoles()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			$get = $conn->prepare("SELECT role FROM User_Role WHERE user = ?");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$roles = [];
			
			foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $role_id)
			{
				$role = new Role($role_id);
				if($role->load())
					$roles[] = $role;
			}
			
			return $roles;
		}
		
		/**
		* Checks if the user has the specified permission
		* @param permission The permission to check
		* @return Does the user have it?
		*/
		public function hasPermission($permission)
		{
			foreach($this->getRoles() as $role)
			{
				// If the permission is in any of the roles the user has
				if(in_array($permission, $role->getPermissions()))
					return TRUE;
			}
			
			return FALSE;
		}
		
		/**
		* Invalidates all non-expired tokens for this user
		*/
		public function invalidateTokens()
		{
			if(!isset($this->id))
				return FALSE;
			
			global $conn;
			$get = $conn->prepare("SELECT token FROM Token WHERE user = ? AND expired = 0");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $token_id)
			{
                $token = new Token($token_id);
                if ($token->load())
                    $token->invalidate();
            }
		}
		
		/**
		* Adds a role to the user
		* @param roleId ID of role to add
		* @return Was the role added?
		*/
		public function addRole($roleId)
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$add = $conn->prepare("INSERT INTO User_Role VALUES (?, ?)");
			$add->bindParam(1, $this->id);
			$add->bindParam(2, $roleId);
			$add->execute();
			
			if($add->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Removes a role from the user
		* @param roleId ID of role to remove
		* @return Was the role removed?
		*/
		public function removeRole($roleId)
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$remove = $conn->prepare("DELETE FROM User_Role WHERE user = ? AND role = ?");
			$remove->bindParam(1, $this->id);
			$remove->bindParam(2, $roleId);
			$remove->execute();
			
			if($remove->rowCount() == 1)
				return TRUE;
			
			return FALSE;
		}
		
		/**
		* Returns non-deleted notifications for this user
		* @return Array of Notification objects, sorted by time, newest first
		*/
		public function getNotifications()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			$get = $conn->prepare("SELECT id FROM Notification WHERE user = ? AND deleted = 0 ORDER BY time DESC");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			$notifications = [];
			
			foreach($get->fetchAll(PDO::FETCH_COLUMN, 0) as $notificationId)
			{
				$notification = new Notification($notificationId);
				if($notification->load())
					$notifications[] = $notification;
			}
			
			return $notifications;
		}
		
		/**
		* @return count of unread notifications
		*/
		public function getUnreadNotificationCount()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			
			$get = $conn->prepare("SELECT id FROM Notification WHERE user = ? AND deleted = 0 AND `read` = 0");
			$get->bindParam(1, $this->id);
			$get->execute();
			
			return $get->rowCount();
		}
		
		/**
		* Removes the user's last visited page records
		*/
		public function deletePageLastVisitRecords()
		{
			if(!isset($this->id))
				return FALSE;
			global $conn;
			$delete = $conn->prepare("DELETE FROM PageLastVisit WHERE user = ?");
			$delete->bindParam(1, $this->id);
			
			$delete->execute();
		}
		
		/**
		* Updates this user's details based on LDAP directory
		* Only used by login script
		*/
		private function refreshLDAPDetails()
		{
			if(!isset($this->id))
				return FALSE;
			if($this->authType != "ldap")
				return FALSE;
			
			$ldap = new LDAPConnection(LDAP_DOMAIN_CONTROLLER, LDAP_DOMAIN, LDAP_DOMAIN_DN);
			$ldap->startTLS();
			
			if(!$ldap->bind(LDAP_USERNAME, LDAP_PASSWORD))
				throw new AppException("Failed To Bind LDAP Session", "L04");
			
			$attributes = $ldap->searchByUsername($this->username, array("givenname", "sn", "mail"));
			
			if($attributes['count'] == 1)
			{
				$user = $attributes[0];
				$this->firstName = ifSet($user['givenname'][0]) ? $user['givenname'][0] : "";
				$this->lastName = ifSet($user['sn'][0]) ? $user['sn'][0] : "";
				$this->email = ifSet($user['mail'][0]) ? $user['mail'][0] : "";
				$this->put();
			}
		}
		
		/**
		* Update attribute that can be changed by the user under 'My Account'
		*/
		public function updateDetails($vars = [])
		{
			if(!isset($this->id))
				return FALSE;
			if(empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new Validator();
			$errs = [];
			
			// Validation
			if(!ifSet($vars['firstName']) OR !$validator->validLength($vars['firstName'], 1, 30))
			{
				$errs[] = "First Name Must Be Between 1 And 30 Characters";
			}
			
			if(!ifSet($vars['lastName']) OR !$validator->validLength($vars['lastName'], 1, 30))
			{
				$errs[] = "Last Name Must Be Between 1 And 30 Characters";
			}
			
			// Email
			if(!empty($vars['email']) AND !filter_var($vars['email'], FILTER_VALIDATE_EMAIL))
				$errs[] = "Email Is Invalid";
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			$this->firstName = $vars['firstName'];
			$this->lastName = $vars['lastName'];
			$this->email = $vars['email'];
			
			return $this->put();
		}
		
		/**
		* Change the password from 'My Account'
		*/
		public function changePassword($vars = [])
		{
			if(!isset($this->id) OR empty($vars) OR !is_array($vars))
				return FALSE;
			
			/////
			// VALIDATION
			/////
			
			$validator = new Validator();
			$errs = [];
			
			if($this->authType == "ldap" AND LDAP_ENABLED === TRUE)
			{
				$ldap = new LDAPConnection(LDAP_DOMAIN_CONTROLLER, LDAP_DOMAIN, LDAP_DOMAIN_DN);
				if(!isset($vars['password']) OR !$ldap->bind($this->username, $vars['password']))
					$errs[] = "Current Password Is Not Correct";
			}
			else
			{
				if(!ifSet($vars['password']) OR !$this->isCorrectPassword($vars['password']))
				{
					$errs[] = "Current Password Is Not Correct";
				}
			}
			
			// New password validation
			if(!ifSet($vars['new']) OR !$validator->isLongEnough($vars['new'], 8))
			{
				$errs[] = "New Password Must Be 8 Characters Or Longer";
			}
			else if(!ifSet($vars['confirm']) OR $vars['new'] != $vars['confirm'])
			{
				$errs[] = "New Password And Confirm Password Must Match";
			}
			
			if(!empty($errs))
				return $errs;
			
			/////
			// PROCESS
			/////
			
			if($this->authType == "ldap" AND LDAP_ENABLED === TRUE)
			{
				$ldap = new LDAPConnection(LDAP_DOMAIN_CONTROLLER, LDAP_DOMAIN, LDAP_DOMAIN_DN);
				$ldap->startTLS();
				
				if(!$ldap->bind(LDAP_USERNAME, LDAP_PASSWORD))
					throw new AppException("Failed To Bind LDAP Session", "L04");
				
				if($ldap->setUserPassword($this->username, $vars['new']))
				{
					return TRUE;
				}
				else
					return FALSE;
			}
			else
			{
				$this->setPassword($vars['new']);
				return $this->put();
			}
		}
	}
