<?php
	if(!isset($_GET['n']))
		throw new AppException("Notification Not Defined", "P03");
	
	$inboxNotification = new Notification($_GET['n']);
	
	if(!$inboxNotification->load())
		throw new AppException("Notification Invalid", "P04");
	
	if(!$inboxNotification->getUser() == $faCurrentUser->getId())
		throw new AppException("Notification Invalid", "S01");
	
	if(!$inboxNotification->delete())
		throw new AppException("Failed To Delete Notification", "D02");
	
	header("Location: " . SITE_URI . "home/inbox");
	exit();
