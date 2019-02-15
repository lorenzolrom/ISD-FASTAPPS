<?php
	////////////////////
	// ISD-FASTAPPS Platform
	// (c) 2018 LLR Technologies / Info. Systems Development
	// Configuration File
	////////////////////
	
	/* Session Settings */
	define('USER_SESSION_NAME', 'FASTAPPS_USRSESSION');
	
	/* Domain Settings */
	define('SITE_TITLE', 'ISD-FASTAPPS');
	define('SITE_URI', '/');
	define('DEFAULT_PAGE', 'home');
	
	/* Database Settings */
	define('DB_HOST', '');
	define('DB_NAME', '');
	define('DB_USER', 'isd-');
	define('DB_PASSWORD', '');
	
	/* Resource Location Settings */
	define('PATH', dirname(__FILE__));
	define('PATH_CORE', PATH . "/extensions/core/");
	define('PATH_TEMPLATE', PATH . "/templates/");
	define('PATH_EXTENSION', PATH . "/extensions/");
	define('PATH_THEME', PATH . "/public/themes/");
	define('PATH_UPLOAD', PATH . "/public/uploads/");
	
	define('URI_STYLE', SITE_URI . "core/stylesheets/");
	define('URI_MEDIA', SITE_URI . "core/media/");
	define('URI_ICON', SITE_URI . "core/media/icons/");
	define('URI_SCRIPT', SITE_URI . "core/scripts/");
	define('URI_THEME', SITE_URI . "themes/");
	define('URI_UPLOAD', SITE_URI . "uploads/");
	
	/* Debug Settings */
	define('SHOW_ERROR_CODES', FALSE); // Will error codes be shown for exceptions?
	
	/* Themes & Extensions */
	$ENABLED_EXTENSIONS = [''];
	$ENABLED_THEMES = [''];
	define('ICONS_ENABLED', FALSE);
	
	// Results to show for each page of a results table
	define('RESULTS_PER_PAGE', 25);
	
	/* LDAP Settings */
	define('LDAP_ENABLED', FALSE);
	define('LDAP_DOMAIN_CONTROLLER', ''); // LDAP Authentication Server
	define('LDAP_DOMAIN', ''); // Domain prefix for user accounts
	define('LDAP_DOMAIN_DN', '');

	define('LDAP_USERNAME', '');
	define('LDAP_PASSWORD', '');
	
	/* E-MAIL Settings */
	define('EMAIL_ENABLED', FALSE);
	define('EMAIL_HOST', '');
	define('EMAIL_PORT', '');
	define('EMAIL_AUTH', '');
	define('EMAIL_USERNAME', '');
	define('EMAIL_PASSWORD', '');
	define('EMAIL_FROM_ADDRESS', '');
	define('EMAIL_FROM_NAME', '');
	
	/* Version Information */
	define('FA_VERSION', '2018.02.10A');