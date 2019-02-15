<?php
	use servicenter as sc;
	
	$workspace = sc\getDefaultWorkspace();
	
	if($workspace === FALSE)
		throw new AppException("No Workspace Has Been Designated For Requests", "B02");
	
	if(!empty($_POST))
	{		
		$r = new sc\Ticket();
		$r->setWorkspace($workspace->getId());
		
		$create = $r->createRequest($_POST);
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "servicenter/requests/view?w=" . $workspace->getId() . "&t=" . $r->getNumber() . "&NOTICE=Request Created"));
		else if($create === FALSE)
			$faSystemErrors[] = "Could Not Create Request";
	}
?>
<span class="workspace-name"><?=$workspace->getName()?></span>
<div class="button-bar">
	<span class="button form-submit-button" id="request" accesskey="s">Save</span>
	<span class="button back-button" accesskey="c">Cancel</span>
</div>
<form class="table-form form" method="post" id="request-form">
	<h2 class="region-title">Request Information</h2>
	<table class="table-display request-display">
		<tr>
			<td class="required">Title</td>
			<td colspan=3><input name="title" autocomplete="off" maxlength=64 type="text" value="<?=ifSet($_POST['title'])?>"></td>
		</tr>
		<tr>
			<td class="required">Request Type</td>
			<td>
				<select name="type">
					<option value="">No Choice</option>
					<?php
						foreach($workspace->getAttributes('type') as $a)
						{
							?>
							<option value="<?=$a->getId()?>"<?=($a->getId() == ifSet($_POST['type'])) ? " selected" : ""?>><?=$a->getName()?></option>
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
						foreach($workspace->getAttributes('cate') as $a)
						{
							?>
							<option value="<?=$a->getId()?>"<?=($a->getId() == ifSet($_POST['category'])) ? " selected" : ""?>><?=$a->getName()?></option>
							<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Location</td>
			<td><input type="text" name="location" value="<?=ifSet($_POST['location'])?>"></td>
			<td>Desired Due Date</td>
			<td><input autocomplete="off" class="date-input" type="text" name="desiredDueDate" value="<?=ifSet($_POST['desiredDueDate'])?>"></td>
		</tr>
	</table>
	
	<h2 class="region-title">New Update</h2>
	<table class="table-display request-display">
		<tr>
			<td class="required">Description</td>
			<td colspan=3><textarea name="description"><?=ifSet($_POST['description'])?></textarea></td>
		</tr>
	</table>
</form>