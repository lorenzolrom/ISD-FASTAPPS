<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// Login Page
	////////////////////
	require_once(dirname(__FILE__) . "/../config.php");
	require_once(PATH_CORE . "includes/database.php");
	
	// Include class files
	foreach(scandir(PATH_CORE . "models") as $file)
	{
		if($file == "." OR $file == "..")
			continue;
		
		require_once(PATH_CORE . "models/" . $file);
	}
	
	require_once(PATH_CORE . "includes/functions.php");
	
	// If a token is present
	if(isset($_COOKIE[USER_SESSION_NAME]))
	{
		$seCurrentToken = new Token($_COOKIE[USER_SESSION_NAME]);
		if($seCurrentToken->load())
		{
			$user = new User($seCurrentToken->getUser());
			$user->load();
			
			// Check if user is logging out
			if(isset($_GET['logout']))
			{
				if($seCurrentToken->invalidate())
				{
					// Logout User
					$user->logout();
					
					// Expire cookie
					setcookie(USER_SESSION_NAME, "", time() - 3600);
					
					$faSystemNotification = "Successfully Logged Out";
				}
				else
					$faSystemErrors[] = "Failed To Invalidate Login Session";
			}
			// Check if the user is logged in
			else if($seCurrentToken->isValid())
			{
				header("Location: " . SITE_URI . DEFAULT_PAGE); // Redirect to the homepage
				exit();
			}
		}
	}
	
	// Process login
	if(!empty($_POST))
	{
		if(!isset($_POST['username']))
			$_POST['username'] = "";
		
		if(!isset($_POST['password']))
			$_POST['password'] = "";
		
		$_POST['username'] = strtolower($_POST['username']);
		
		try
		{
			// Validate user details
			$faLoginUser = new User();
			$faLoginUser->setUsername($_POST['username']);
			
			if($faLoginUser->loadFromUsername())
			{
				// Have the user's credentials been validated?
				$passwordIsValid = FALSE;
				
				// Validate user password
				if($faLoginUser->getAuthType() == "ldap" AND LDAP_ENABLED === TRUE) // LDAP authentication
				{
					// New LDAP connection
					$ldapConnection = new LDAPConnection(LDAP_DOMAIN_CONTROLLER, LDAP_DOMAIN, LDAP_DOMAIN_DN);					
					$ldapConnection->startTLS();
					
					// Password must not be empty for LDAP authentication
					if(strlen($_POST['password']) == 0)
						throw new AppException("Username or Password is Incorrect", "S06");
					
					if($ldapConnection->bind($_POST['username'], $_POST['password']))
						$passwordIsValid = TRUE;
				}
				else // Local database authentication
				{
					if($faLoginUser->isCorrectPassword($_POST['password']))
						$passwordIsValid = TRUE;
				}
				
				if($passwordIsValid)
				{
					if($faLoginUser->getDisabled() == 0)
					{						
						// Create session token
						$seNewToken = new Token();
						$seNewToken->setUser($faLoginUser->getId());
						$faLoginUser->login();
						
						if($seNewToken->create())
						{
							// Set token in SESSION variables
							setcookie(USER_SESSION_NAME, $seNewToken->getToken(), 0, SITE_URI);
							
							// Redirect to application
							$loginNextLocation = SITE_URI . DEFAULT_PAGE;
							
							if(!empty($_GET['next']) AND $_GET['next'] != SITE_URI)
								$loginNextLocation = $_GET['next'];
							
							header("Location: " . $loginNextLocation);
							exit();
						}
						else
							throw new AppException("Failed To Secure Login Token", "S04");
					}
					else
						throw new AppException("Username or Password is Incorrect", "S05");
				}
				else
					throw new AppException("Username or Password is Incorrect", "S03");
			}
			else
				throw new AppException("Username or Password is Incorrect", "S02");
		}
		catch(AppException $ae)
		{
			$faSystemErrors[] = (SHOW_ERROR_CODES ? ($ae->getCustomCode() . ": ") : "") . $ae->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="<?=URI_STYLE?>Login.css">
		<link rel="stylesheet" type="text/css" href="<?=URI_SCRIPT?>jquery-ui-1.12.1/jquery-ui.min.css">
		<script src="<?=URI_SCRIPT?>jquery-3.3.1.min.js"></script>
		<script src="<?=URI_SCRIPT?>jquery-ui-1.12.1/jquery-ui.min.js"></script>
		<script src="<?=URI_SCRIPT?>core.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?="Login | " . SITE_TITLE?></title>
	</head>
	<body>
		<form id="login-window" method="post" action="login.php?next=<?=ifSet($_GET['next'])?>">
			<span id="login-logo"></span>
			<span id="login-title"><?=SITE_TITLE?></span>
			<?php
				require_once(PATH_TEMPLATE . "notifications.php");
			?>
			<p>
				<input name="username" type="text" value="<?=ifSet($_POST['username'])?>" placeholder="Username">
			</p>
			<p>
				<input name="password" type="password" placeholder="Password">
			</p>
			<input id="login-button" type="submit" value="Log in">
		</form>
	</body>
</html>
<?php
	$conn->close();
?>
<script>
	$('#login-window').submit(function(){
		$('#login-button').val("Logging in...");
	});
</script>