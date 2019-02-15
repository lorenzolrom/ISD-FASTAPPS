<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// Session Verification Script
	////////////////////
	
	if(isset($_COOKIE[USER_SESSION_NAME]))
	{
		$faCurrentToken = new Token($_COOKIE[USER_SESSION_NAME]);
		
		
		if($faCurrentToken->load())
		{
			// Creates the user object for this session
			$faCurrentUser = new User($faCurrentToken->getUser());
			$faCurrentUser->load();
			
			if($faCurrentToken->isValid() AND $faCurrentUser->getDisabled() != 1)
			{
				// Update token expire time
				if(!$faCurrentToken->updateExpireTime())
				{
					throw new AppException("Update Action Failed", "D02");
				}
			}
			else
			{
				// Invalidate token
				$faCurrentToken->invalidate();
				
				// Expire cookie
				setcookie(USER_SESSION_NAME, "", time() - 3600);
				
				// Redirect to login -> token expired
				header("Location: " . SITE_URI . "login.php?next=" . getURI() . "&NOTICE=Token Expired");
				exit();
			}
		}
		else
		{
			// Expire cookie
			setcookie(USER_SESSION_NAME, "", time() - 3600);
				
			// Redirect to login -> invalid token
			header("Location: " . SITE_URI . "login.php?next=" . getURI() . "&NOTICE=Invalid Token");
			exit();
		}
	}
	else
	{
		// Expire cookie
		setcookie(USER_SESSION_NAME, "", time() - 3600);
			
		// Redirect to login
		header("Location: " . SITE_URI . "login.php?next=" . getURI());
		exit();
	}
