<?php use servicenter as sc;

	// Load Workspace
	if(!isset($_GET['w']))
		throw new AppException('Workspace Not Defined', 'P03');
	$workspace = new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException('Workspace Not Found', 'P04');
	
	// Load Ticket
	if(!isset($_GET['t']))
		throw new AppException('Ticket Not Defined', 'P03');
	$ticket = new sc\Ticket();
	if(!$ticket->loadFromNumber($workspace->getId(), $_GET['t']))
		throw new AppException('Ticket Not Found', 'P04');
	
	// Check User
	if($ticket->getContact() !== $faCurrentUser->getId())
		throw new AppException('Ticket Not Found', 'S01');
	
	// Check Status
	if($ticket->getStatus() == "clos")
		throw new AppException('Ticket Has Been Closed', 'P04');
	
	if(!empty($_POST))
	{
		$update = $ticket->updateRequest($_POST);
		if(is_array($update))
			$faSystemErrors = $update;
		else if($update === TRUE)
			exit(header("Location: " . SITE_URI . "servicenter/requests/view?w=" . $workspace->getId() . "&t=" . $ticket->getNumber() . "&NOTICE=Request Updated"));
		else
			$faSystemErrors[] = "Could Not Update Request";
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<span class="button form-submit-button" id="update" accesskey="s">Save</span>
	<a class="button" href="<?=SITE_URI . "servicenter/requests/view?w=" . $workspace->getId() . "&t=" . $ticket->getNumber()?>" accesskey="c">Cancel</a>
</div>
<form class="table-form form" method="post" id="update-form">
	<h2 class="region-title">Update <?=$workspace->getName()?> Ticket #<?=$ticket->getNumber()?></h2>
	<table class="table-display request-display">
		<tr>
			<td class="required">New Description</td>
			<td colspan=3><textarea name="description"><?=ifSet($_POST['description'])?></textarea></td>
		</tr>
	</table>
</form>