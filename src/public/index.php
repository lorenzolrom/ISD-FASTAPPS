<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// All-In-One Index Page
	////////////////////
	
	/// Allow header() redirects to work
	ob_start();
	
	$faCurrentPageTitle = "Not Found"; // Set default page title
	
	try
	{
		// Include Configuration File
		require_once(dirname(__FILE__) . "/../config.php");
		
		// Include base files
		require_once(PATH_CORE . "includes/database.php"); // Database connection
	
		// Include class files
		foreach(scandir(PATH_CORE . "models") as $file)
		{
			if($file == "." OR $file == "..")
				continue;
			
			require_once(PATH_CORE . "models/" . $file);
		}
		
		require_once(PATH_CORE . "includes/functions.php"); // Global functions
		require_once(PATH_CORE . "includes/session.php"); // Check session
		
		// Include files from enabled extensions
		if(!empty($ENABLED_EXTENSIONS))
		{
			foreach($ENABLED_EXTENSIONS as $extension)
			{
				$includePath = PATH_EXTENSION . $extension . "/includes";
				$classPath = PATH_EXTENSION . $extension . "/models";
				
				if(is_dir($includePath))
				{
					$configFile = PATH_EXTENSION . $extension . "/config.php";
					if(is_file($configFile))
						require_once($configFile); // Configuration file
					
					foreach(scandir($includePath) as $file) // Includes
					{
						if($file == "." OR $file == "..")
							continue;
						
						require_once($includePath . "/" . $file);
					}
				}
				
				if(is_dir($classPath))
				{
					foreach(scandir($classPath) as $file) // Includes
					{
						if($file == "." OR $file == "..")
							continue;
						
						require_once($classPath . "/" . $file);
					}
				}
			}
		}
		
		// If email is enabled
		if(EMAIL_ENABLED)
			require_once(PATH_CORE . "includes/mail.php");
	
		// Retrieve URL
        $faCurrentURL = "";
        if(isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
            $faCurrentURL = "https";
        else
            $faCurrentURL = "http";

        $faCurrentURL .= "://";

        $faCurrentURL .= $_SERVER['HTTP_HOST'];
        $faCurrentURL .= $_SERVER['REQUEST_URI'];

        $faCurrentURI = "/" . explode(SITE_URI, $faCurrentURL)[1];

		$faCurrentURIParts = explode("/", explode("?",$faCurrentURI)[0]);
		
		// If no uri present, finds the first section the user has permission for
		if(empty($faCurrentURIParts[1]))
		{
			foreach(getSections() as $section)
			{
				if($faCurrentUser->hasPermission($section->getPermission()))
				{
					header("Location: " . SITE_URI . $section->getURL());
					exit();
				}
			}
		}
		
		// Create Section page
		$faCurrentSection = new Page();
		$faCurrentSection->setUrl($faCurrentURIParts[1]);
		
		// Holder for the page
		$faCurrentPage = null;
		
		if($faCurrentSection->loadFromURL())
		{			
			// Check section permission
			if($faCurrentUser->hasPermission($faCurrentSection->getPermission()))
			{
				$faPageURI = "";
				
				// Only section has been supplied
				if(empty($faCurrentURIParts[2]))
				{
					// Find first page in Section user has permission for
					foreach($faCurrentSection->getChildren(TRUE) as $page)
					{
						if($faCurrentUser->hasPermission($page->getPermission()))
						{
							// Redirect to this page
							header("Location: " . SITE_URI . $page->getURL());
							exit();
						}
					}
				}
				
				// Build complete URI
				for($i = 1; $i < sizeof($faCurrentURIParts); $i++)
				{
					$faPageURI .= $faCurrentURIParts[$i] . "/";
				}
				$faPageURI = rtrim($faPageURI, "/"); //Remove trailing /
				
				// Create new page
				$faCurrentPage = new Page();
				$faCurrentPage->setUrl($faPageURI);
				if(($faCurrentPage->loadFromURL()) AND ($faCurrentPage->getPageType() == 'page'))
				{
					// Check permission
					if($faCurrentUser->hasPermission($faCurrentPage->getPermission()))
					{					
						$faCurrentPageTitle = $faCurrentPage->getTitle();
						
						$faIsPageFound = TRUE;
						
						// Set GET variables
						if(empty($_GET) OR (sizeof($_GET) == 1 AND array_keys($_GET)[0] == 'NOTICE'))
						{
							$lastGetVars = $faCurrentPage->getLastGetVars();
							
							if($lastGetVars !== FALSE) // Found last get variables
							{
								if(sizeof($_GET) == 1 AND array_keys($_GET)[0] == 'NOTICE')
									$lastGetVars .= '&NOTICE=' . $_GET['NOTICE'];
								
								header("Location: " . SITE_URI . $faCurrentPage->getURL() . $lastGetVars);
								exit();
							}
						}
						else
						{							
							$faCurrentPage->setLastGetVars($_GET);
						}
					}
					else
						throw new AppException("You Do Not Have Permission To View This Page", "S01");
				}
				else
					throw new AppException("Page Not Found", "P02");
			}
			else
				throw new AppException("You Do Not Have Permission To View This Page", "S01");
		}
		else
			throw new AppException("Page Not Found", "P01");
	}
	catch(AppException $ae)
	{
		$faSystemErrors[] = (SHOW_ERROR_CODES ? ($ae->getCustomCode() . ": ") : "") . $ae->getMessage();
	}
	catch(Exception $e)
	{
		$faSystemErrors[] = (SHOW_ERROR_CODES ? ("X01: ") : "") . "Unknown Error Has Occurred";
	}
	
	// Load Page
	require_once(PATH_TEMPLATE . "_head.php");
	if(isset($faIsPageFound))
	{
		try
		{			
			// Include page templates
			require_once(PATH_EXTENSION . $faCurrentPage->getExtension() . "/views/" . $faCurrentPage->getURL() . ".php");
		} catch(Exception $e)
		{
			$faSystemErrors[] = (SHOW_ERROR_CODES ? ("X01: ") : "") . "Unknown Error Has Occurred";
		}
	}
	require_once(PATH_TEMPLATE . "notifications.php");
	require_once(PATH_TEMPLATE . "_foot.php");
