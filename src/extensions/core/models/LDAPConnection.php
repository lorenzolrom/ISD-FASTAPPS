<?php
	/**
	* An LDAP Connection
	*/
	class LDAPConnection
	{
		private $ldapConnection;
		private $domainController;
		private $ldapBind;
		private $bound;
		private $domain;
		private $domainDN;
		
		public function __construct($domainController, $domain, $domainDN)
		{
			$this->bound = FALSE;
			$this->domainController = $domainController;
			$this->domain = $domain;
			$this->domainDN = $domainDN;
			
			// Create LDAP connection object
			$this->ldapConnection = ldap_connect($domainController);
		}
		
		/**
		* Starts a TLS LDAP Connection
		*/
		public function startTLS()
		{
			if(!ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3)) // LDAP version 3
				throw new AppException("Failed To Set LDAP Protocol Version", "L01");
				
			if(!ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0)) // Disable LDAP referrals
				throw new AppException("Failed To Disable LDAP Referrals", "L01");
				
			if(!ldap_start_tls($this->ldapConnection))
				throw new AppException("Failed To Start TLS LDAP Connection", "L02");
		}
		
		/**
		* Bind to directory using supplied details
		* @return Was the bind successful?
		*/
		public function bind($username, $password)
		{
			set_error_handler(function(){}); // Prevent incorrect LDAP login from throwing warning
			
			if(ldap_bind($this->ldapConnection, $this->domain . "\\" . $username, $password))
			{
				$this->bound = TRUE;
				restore_error_handler();
				return TRUE;
			}
			
			restore_error_handler();
			return FALSE;
		}
		
		/**
		* Searches for a user by username and returns specified attributes
		*/
		public function searchByUsername($username, $attributes)
		{
			$filter = "(|(sAMAccountName=" . $username . "))";
			$search = ldap_search($this->ldapConnection, $this->domainDN, $filter, $attributes);
			return ldap_get_entries($this->ldapConnection, $search);
		}
		
		public function setUserPassword($username, $newPassword)
		{
			$ldapAttributes = array("uid");
			$user = $this->searchByUsername($username, $ldapAttributes);
			
			if($user['count'] != 1)
				return FALSE;
			
			$resultUserDN = $user[0]['dn']; // Get LDAP DN of user
			
			$newLDAPEntry = array('unicodePwd' => mb_convert_encoding(("\"" . $newPassword . "\""), 'UTF-16LE')); // Create new password entry for LDAP user
			
			if(ldap_mod_replace($this->ldapConnection, $resultUserDN, $newLDAPEntry))
				return TRUE;
			
			return FALSE;
		}
	}
