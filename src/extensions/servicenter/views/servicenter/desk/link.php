<?php
	use servicenter as sc;
	
	// Are ticket details set
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	if(!isset($_GET['t']))
		throw new AppException("Ticket Not Defined", "P03");
	
	// Is Workspace Valid?
	$workspace = new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Not Found", "P04");
	
	// Does user have permission for workspace?
	$userInWorkspace = FALSE;
	
	foreach(sc\getUserWorkspaces() as $ws)
	{
		if($ws->getId() == $workspace->getId())
			$userInWorkspace = TRUE;
	}
	
	if(!$userInWorkspace)
		throw new AppException("You Are Not A Member Of This Workspace", "S01");
	
	// Is Ticket valid
	$ticket = new sc\Ticket();
	if(!$ticket->loadFromNumber($workspace->getId(), $_GET['t']))
		throw new AppException("Ticket Not Found", "P04");
	
	// Are we unlinking the ticket?
	if(isset($_GET['unlink']))
	{
		// Is unlink ticket valid?
		$unlink = new sc\Ticket();
		if(!$unlink->loadFromNumber($workspace->getId(), $_GET['unlink']))
			throw new AppException("Unlink Ticket Not Found", "P05");
		
		if($ticket->unlinkTicket($unlink->getId()) === TRUE)
		{
			exit(header("Location: " . getURI() . "/../view?w=" . $workspace->getId() . "&t=" . $ticket->getNumber() . "&NOTICE=Ticket " . $unlink->getNumber() . " Has Been Un-Linked"));
		}
		else
			throw new AppException("Unable To Unlink Supplied Ticket", "P05");
	}
	
	if(isset($_POST['link-ticket-number']) AND isset($_POST['link-type']))
	{
		$linkTicket = new sc\Ticket();
		$linkTicket->loadFromNumber($workspace->getId(), $_POST['link-ticket-number']);
		
		$link = $ticket->linkTicket($linkTicket->getId(), $_POST['link-type']);
		
		if(is_array($link))
			$faSystemErrors = $link;
		else if($link === TRUE)
			exit(header("Location: view?w=" . $workspace->getId() . "&t=" . $ticket->getNumber() . "&NOTICE=Ticket " . $_POST['link-ticket-number'] . " Has Been Linked"));
		else
			$faSystemErrors[] = "Could Not Link Ticket";
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<span class="button form-submit-button" id="link" accesskey="l">Link</span>
	<a class="button" href="<?=getURI()?>/../view?w=<?=$workspace->getId()?>&t=<?=$ticket->getNumber()?>">Cancel</a>
</div>
<form id="link-form" class="table-form form" method="post">
	<h2 class="region-title">Link <?=$workspace->getName()?> Ticket #<?=$ticket->getNumber()?></h2>
	<table class="table-display">
		<tr>
			<td>Ticket #</td>
			<td colspan=2><input type="text" name="link-ticket-number" value="<?=ifSet($_POST['link-ticket-number'])?>"></td>
		</tr>
		<tr>
			<td>Link Type</td>
			<td><input type="radio" name="link-type" value="s" checked> Static
			<input type="radio" name="link-type" value="d"> Dynamic</td>
		</tr>
	</table>
</form>