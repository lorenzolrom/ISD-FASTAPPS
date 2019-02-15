<?php
	use servicenter as sc;
	
	// Verify workspace exists and current user is a member
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace = new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Not Found", "P04");
	
	$userInWorkspace = FALSE;
		
	foreach(sc\getUserWorkspaces() as $ws)
	{
		if($ws->getId() == $workspace->getId())
			$userInWorkspace = TRUE;
	}
	
	if(!$userInWorkspace)
		throw new AppException("You Are Not A Member Of This Workspace", "S01");
	
	$ticket = null;
	
	// Verify ticket if we are editing an existing
	if(!isset($_GET['new']))
	{	
		if(!isset($_GET['t']))
			throw new AppException("Ticket Not Defined", "P03");
	
		$ticket = new sc\Ticket();
		
		if(!$ticket->loadFromNumber($workspace->getId(), $_GET['t']))
			throw new AppException("Ticket Not Found", "P04");
	}
	
	// Process form
	if(!empty($_POST))
	{
		if(!isset($_GET['new']) AND $ticket !== NULL) // Edit existing
		{
			$save = $ticket->save($_POST);
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === TRUE)
				$notice = "Ticket Updated";
			else
				$faSystemErrors[] = "Could Not Update Ticket";
		}
		else // Create new
		{
			$ticket = new sc\Ticket();
			$create = $ticket->create($_POST);
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				$notice = "Ticket Created";
			else
				$faSystemErrors[] = "Could Not Create Ticket";
		}
	}
	
	if(isset($notice))
		exit(header("Location: " . getURI() . "/../view?w=" . $workspace->getId() . "&t=" . $ticket->getNumber() . "&NOTICE=" . $notice));
	
	if(!isset($_GET['new']) AND $ticket !== NULL)
	{
		// Populate POST Variables		
		$contact = $ticket->getContact();
		
		if($contact !== NULL)
		{
			$contact = new User($ticket->getContact());
			if($contact->load())
				$_POST['contactUsername'] = $contact->getUsername();
		}
		
		$_POST['title'] = $ticket->getTitle();
		$_POST['status'] = $ticket->getStatus();
		$_POST['closureCode'] = $ticket->getClosureCode();
		$_POST['priority'] = $ticket->getPriority();
		$_POST['workScale'] = $ticket->getScale();
		$_POST['desiredDueDate'] = $ticket->getDesiredDueDate();
		$_POST['targetDate'] = $ticket->getTargetDate();
		$_POST['workScheduleDate'] = $ticket->getWorkScheduleDate();
		$_POST['nextReviewDate'] = $ticket->getNextReviewDate();
		$_POST['vendorInfo'] = $ticket->getVendorInfo();
		$_POST['location'] = $ticket->getLocation();
		$_POST['assignees'] = $ticket->getAssignees();
		
		/////
		// I.D. -> Code
		/////
		
		// Category
		$category = new sc\WorkspaceAttribute($ticket->getCategory());
		$category->load();
		$_POST['category'] = $category->getCode();
		
		// Severity
		$severity = new sc\WorkspaceAttribute($ticket->getSeverity());
		$severity->load();
		$_POST['severity'] = $severity->getCode();
		
		// Source
		$source = new Attribute($ticket->getSource());
		$source->load();
		$_POST['source'] = $source->getCode();
		
		// Type
		$type = new sc\WorkspaceAttribute($ticket->getTicketType());
		$type->load();
		$_POST['type'] = $type->getCode();
	}
	
	if(!isset($_POST['assignees']) OR !is_array($_POST['assignees']))
		$_POST['assignees'] = [];
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<span class="button form-submit-button" id="ticket" accesskey="s">Save</span>
	<?php
		if(isset($_GET['new']))
		{
		?>
		<span class="button back-button" accesskey="c">Cancel</span>
		<?php
		}
		else
		{
			?>
			<a class="button" href="<?=getURI()?>/../view?w=<?=$workspace->getId()?>&t=<?=$ticket->getNumber()?>">Cancel</a>
			<?php
		}
	?>
</div>
<form class="table-form form" method="post" id="ticket-form">
	<input type="hidden" name="workspace" value="<?=$workspace->getId()?>">
	<h2 class="region-title">Ticket Profile<?=($ticket !== null) ? (" #" . $ticket->getNumber()) : ""?></h2>
	<table class="table-display ticket-display">
		<tr>
			<td class="required">Title</td>
			<td colspan=3><input type="text" maxlength=64 name="title" value="<?=htmlentities(ifSet($_POST['title']))?>"></td>
		</tr>
	</table>
	
	<h2 class="region-title">Contact Information</h2>
	<table class="table-display ticket-display">
		<tr>
			<td>Contact Username</td>
			<td><input type="text" name="contactUsername" value="<?=ifSet($_POST['contactUsername'])?>"></td>
		</tr>
	</table>
	
	<h2 class="region-title">Ticket Information</h2>
	<table class="table-display ticket-display">
		<tr>
			<td class="required">Status</td>
			<td>
				<select name="status" id="status">
					<option value="">No Choice</option>
					<?php
						foreach(array_merge(getAttributes('srvc', 'tsta'), $workspace->getAttributes('tsta')) as $status)
						{
							?>
							<option value="<?=$status->getCode()?>"<?=ifSet($_POST['status']) == $status->getCode() ? " selected" : ""?>><?=$status->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
			<td class="closureCode required" >Closure Code</td>
			<td class="closureCode">
				<select name="closureCode">
					<option value="">No Choice</option>
					<?php
						foreach(array_merge(getAttributes('srvc', 'tclc'), $workspace->getAttributes('tclc')) as $closureCode)
						{
							?>
							<option value="<?=$closureCode->getCode()?>"<?=ifSet($_POST['closureCode']) == $closureCode->getCode() ? " selected" : ""?>><?=$closureCode->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Type</td>
			<td>
				<select name="type">
					<option value="">No Choice</option>
					<?php
						foreach($workspace->getAttributes('type') as $type)
						{
							?>
							<option value="<?=$type->getCode()?>"<?=ifSet($_POST['type']) == $type->getCode() ? " selected" : ""?>><?=$type->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
			<td class="required">Category</td>
			<td>
				<select name="category">
					<option value="">No Choice</option>
					<?php
						foreach($workspace->getAttributes('cate') as $category)
						{
							?>
							<option value="<?=$category->getCode()?>"<?=ifSet($_POST['category']) == $category->getCode() ? " selected" : ""?>><?=$category->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Severity</td>
			<td>
				<select name="severity">
					<option value="">No Choice</option>
					<?php
						foreach($workspace->getAttributes('seve') as $severity)
						{
							?>
							<option value="<?=$severity->getCode()?>"<?=ifSet($_POST['severity']) == $severity->getCode() ? " selected" : ""?>><?=$severity->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
			<td>Priority</td>
			<td>
				<select name="priority">
					<option value="0">Not Set</option>
					<?php
						for($i = 1; $i <= $workspace->getPriorityLevels(); $i++)
						{
							?>
							<option value="<?=$i?>"<?=ifSet($_POST['priority']) == $i ? " selected" : ""?>><?=$i?></option>
							<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Work Scale</td>
			<td>
				<select name="workScale">
					<option value="0">Not Set</option>
					<?php
						for($i = 1; $i <= $workspace->getScaleLevels(); $i++)
						{
							?>
							<option value="<?=$i?>"<?=ifSet($_POST['workScale']) == $i ? " selected" : ""?>><?=$i?></option>
							<?php
						}
					?>
				</select>
			</td>
			<td class="required">Source</td>
			<td>
				<select name="source">
					<option value="">No Choice</option>
					<?php
						foreach(\getAttributes('srvc', 'tsrc') as $source)
						{
							?>
							<option value="<?=$source->getCode()?>"<?=ifSet($_POST['source']) == $source->getCode() ? " selected" : ""?><?=(!isset($_POST['source']) AND $source->getCode() == "self") ? " selected" : ""?>><?=$source->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
		</tr>
	</table>
	
	<h2 class="region-title">Additional Ticket Information</h2>
	<table class="table-display ticket-display">
		<tr>
			<td>Desired Due Date</td>
			<td><input class="date-input" name="desiredDueDate" type="text" autocomplete="off" value="<?=ifSet($_POST['desiredDueDate'])?>"></td>
			<td>Target Date</td>
			<td><input class="date-input" name="targetDate" type="text" autocomplete="off" value="<?=ifSet($_POST['targetDate'])?>"></td>
		</tr>
		<tr>
			<td>Work Scheduled Date</td>
			<td><input class="date-input" name="workScheduleDate" type="text" autocomplete="off" value="<?=ifSet($_POST['workScheduleDate'])?>"></td>
			<td>Next Review Date</td>
			<td><input class="date-input" name="nextReviewDate" type="text" autocomplete="off" value="<?=ifSet($_POST['nextReviewDate'])?>"></td>
		</tr>
		<tr>
			<td>Vendor Info</td>
			<td><input type="text" name="vendorInfo" value="<?=htmlentities(ifSet($_POST['vendorInfo']))?>"></td>
			<td>Location</td>
			<td><input type="text" name="location" value="<?=htmlentities(ifSet($_POST['location']))?>"></td>
		</tr>
	</table>
	
	<h2 class="region-title">Append Description</h2>
	<table class="table-display ticket-display">
		<tr>
			<td>Description</td>
			<td colspan=3><textarea name="description"><?=ifSet($_POST['description'])?></textarea></td>
		</tr>
	</table>
	
	<h2 class="region-title">Assignees & Notifications</h2>
	<table class="table-display ticket-display">
		<tr>
			<td>Assign Teams/Users</td>
			<td>
				<select class="assignee-select" name="assignees[]" multiple size="10">
					<?php
						foreach($workspace->getTeams() as $team)
						{
							?>
							<option class="team" value="<?=$team->getId()?>"<?=in_array($team->getId(), $_POST['assignees']) ? " selected" : ""?>><?=$team->getName()?></option>
							<?php
							foreach($team->getMembers() as $member)
							{
								?>
								<option class="member" value="<?=$team->getId()?>-<?=$member->getId()?>"<?=in_array($team->getId() . "-" . $member->getId(), $_POST['assignees']) ? " selected" : ""?>><?=$member->getFirstName() . " " . $member->getLastName()?></option>
								<?php
							}
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Notify Assignees</td>
			<td><input type="checkbox" name="notify-assignees" value="true"></td>
		</tr>
		<tr>
			<td>Notify Contact</td>
			<td><input type="checkbox" name="notify-contact" value="true"></td>
		</tr>
	</table>
</form>
<script>

	$(document).ready(function(){
		ServiCenter_ticketform_checkStatus();
		
		$('#status').change(function(){
			ServiCenter_ticketform_checkStatus();
		});
	});
	
	function ServiCenter_ticketform_checkStatus()
	{
		var status = $('#status').val();
		if(status == 'clos')
		{
			$.each($(document).find(".closureCode"), function(index, value){
				$(value).show();
			});
		}
		else
		{
			$.each($(document).find(".closureCode"), function(index, value){
				$(value).hide();
			});
		}
	}
</script>