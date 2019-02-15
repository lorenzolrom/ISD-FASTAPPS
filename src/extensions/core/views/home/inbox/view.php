<?php
	if(!isset($_GET['n']))
		throw new AppException("Notification Not Defined", "P03");
	
	$inboxNotification = new Notification($_GET['n']);
	
	if(!$inboxNotification->load())
		throw new AppException("Notification Invalid", "P04");
	
	if(!$inboxNotification->getUser() == $faCurrentUser->getId())
		throw new AppException("Notification Invalid", "S01");
	
	// Mark notification as read
	if(!$inboxNotification->read())
		throw new AppException("Failed To Open Notification", "D02");
	
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/../delete?n=<?=$inboxNotification->getId()?>">Delete</a>
	<span class="button back-button">Back</span>
</div>
<div class="basic-display display">
	<div>
		<span>Title</span>
		<span><?=htmlentities($inboxNotification->getTitle())?></span>
	</div>
	<div>
		<span>Received</span>
		<span><?=$inboxNotification->getTime()?></span>
	</div>
	<div>
		<span>Message</span>
		<p><?=htmlentities($inboxNotification->getData())?></p>
	</div>
</div>