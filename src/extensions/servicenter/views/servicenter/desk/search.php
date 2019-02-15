<?php
	use servicenter as sc;
	// Validate Workspace #/Permissions
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
	
	// No search submitted--show search form
	if(!isset($_GET['submitted']))
	{
		if(!isset($_GET['assignees']) OR !is_array($_GET['assignees']))
			$_GET['assignees'] = [];
	
		?>
		<div class="button-bar">
			<span class="button form-submit-button" id="search" accesskey="s">Search</span>
			<span class="button back-button" accesskey="c">Cancel</span>
		</div>
		<form class="table-form form" id="search-form">
			<input type="hidden" name="w" value="<?=$workspace->getId()?>">
			<input type="hidden" name="submitted" value="yes">
			<h2 class="region-title">Ticket Profile</h2>
			<table class="table-display ticket-display">
				<tr>
					<td>Title</td>
					<td colspan=3><input type="text" name="title" value="<?=htmlentities(ifSet($_GET['title']))?>"></td>
				</tr>
			</table>
			
			<h2 class="region-title">Contact Information</h2>
			<table class="table-display ticket-display">
				<tr>
					<td>Contact Username</td>
					<td><input type="text" name="contactUsername" value="<?=ifSet($_GET['contactUsername'])?>"></td>
				</tr>
			</table>
			
			<h2 class="region-title">Ticket Information</h2>
			<table class="table-display ticket-display">
				<tr>
					<td>Status</td>
					<td>
						<select name="status" id="status" multiple size=5>
							<?php
								foreach(array_merge(getAttributes('srvc', 'tsta'), $workspace->getAttributes('tsta')) as $status)
								{
									?>
									<option value="<?=$status->getCode()?>"<?=ifSet($_GET['status']) == $status->getCode() ? " selected" : ""?>><?=$status->getName()?></option>
									<?php
								}
							?>
						</select>
					</td>
					<td>Closure Code</td>
					<td>
						<select name="closureCode" multiple size=5>
							<?php
								foreach(array_merge(getAttributes('srvc', 'tclc'), $workspace->getAttributes('tclc')) as $closureCode)
								{
									?>
									<option value="<?=$closureCode->getCode()?>"<?=ifSet($_GET['closureCode']) == $closureCode->getCode() ? " selected" : ""?>><?=$closureCode->getName()?></option>
									<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Type</td>
					<td>
						<select name="type" multiple size=5>
							<?php
								foreach($workspace->getAttributes('type') as $type)
								{
									?>
									<option value="<?=$type->getCode()?>"<?=ifSet($_GET['type']) == $type->getCode() ? " selected" : ""?>><?=$type->getName()?></option>
									<?php
								}
							?>
						</select>
					</td>
					<td>Category</td>
					<td>
						<select name="category" multiple size=5>
							<?php
								foreach($workspace->getAttributes('cate') as $category)
								{
									?>
									<option value="<?=$category->getCode()?>"<?=ifSet($_GET['category']) == $category->getCode() ? " selected" : ""?>><?=$category->getName()?></option>
									<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Severity</td>
					<td>
						<select name="severity" multiple size=5>
							<?php
								foreach($workspace->getAttributes('seve') as $severity)
								{
									?>
									<option value="<?=$severity->getCode()?>"<?=ifSet($_GET['severity']) == $severity->getCode() ? " selected" : ""?>><?=$severity->getName()?></option>
									<?php
								}
							?>
						</select>
					</td>
					<td>Priority</td>
					<td>
						<select name="priority" multiple size=5>
							<option value="0">Not Set</option>
							<?php
								for($i = 1; $i <= $workspace->getPriorityLevels(); $i++)
								{
									?>
									<option value="<?=$i?>"<?=ifSet($_GET['priority']) == $i ? " selected" : ""?>><?=$i?></option>
									<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Work Scale</td>
					<td>
						<select name="workScale" multiple size=5>
							<option value="0">Not Set</option>
							<?php
								for($i = 1; $i <= $workspace->getScaleLevels(); $i++)
								{
									?>
									<option value="<?=$i?>"<?=ifSet($_GET['workScale']) == $i ? " selected" : ""?>><?=$i?></option>
									<?php
								}
							?>
						</select>
					</td>
					<td>Source</td>
					<td>
						<select name="source" multiple size=5>
							<?php
								foreach(\getAttributes('srvc', 'tsrc') as $source)
								{
									?>
									<option value="<?=$source->getCode()?>"<?=ifSet($_GET['source']) == $source->getCode() ? " selected" : ""?>><?=$source->getName()?></option>
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
					<td><input class="date-input" name="desiredDueDate" type="text" autocomplete="off" value="<?=ifSet($_GET['desiredDueDate'])?>"></td>
					<td>Target Date</td>
					<td><input class="date-input" name="targetDate" type="text" autocomplete="off" value="<?=ifSet($_GET['targetDate'])?>"></td>
				</tr>
				<tr>
					<td>Work Scheduled Date</td>
					<td><input class="date-input" name="workScheduleDate" type="text" autocomplete="off" value="<?=ifSet($_GET['workScheduleDate'])?>"></td>
					<td>Next Review Date</td>
					<td><input class="date-input" name="nextReviewDate" type="text" autocomplete="off" value="<?=ifSet($_GET['nextReviewDate'])?>"></td>
				</tr>
				<tr>
					<td>Vendor Info</td>
					<td><input type="text" name="vendorInfo" value="<?=htmlentities(ifSet($_GET['vendorInfo']))?>"></td>
					<td>Location</td>
					<td><input type="text" name="location" value="<?=htmlentities(ifSet($_GET['location']))?>"></td>
				</tr>
			</table>
			
			<h2 class="region-title">Description</h2>
			<table class="table-display ticket-display">
				<tr>
					<td>Description</td>
					<td colspan=3><textarea name="description"><?=ifSet($_GET['description'])?></textarea></td>
				</tr>
			</table>
			
			<h2 class="region-title">Assignees & Notifications</h2>
			<table class="table-display ticket-display">
				<tr>
					<td>Assigned Teams/Users</td>
					<td>
						<select class="assignee-select" name="assignees[]" multiple size="10">
							<?php
								foreach($workspace->getTeams() as $team)
								{
									?>
									<option class="team" value="<?=$team->getId()?>"<?=in_array($team->getId(), $_GET['assignees']) ? " selected" : ""?>><?=$team->getName()?></option>
									<?php
									foreach($team->getMembers() as $member)
									{
										?>
										<option class="member" value="<?=$team->getId()?>-<?=$member->getId()?>"<?=in_array($team->getId() . "-" . $member->getId(), $_GET['assignees']) ? " selected" : ""?>><?=$member->getFirstName() . " " . $member->getLastName()?></option>
										<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
	else
	{
		// Process form
	}
?>