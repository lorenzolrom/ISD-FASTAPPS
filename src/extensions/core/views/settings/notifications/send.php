<?php
	if(!empty($_POST))
	{
		$notification = new Notification();
		
		$create = $notification->sendToRoles($_POST);
		
		if(is_array($create))
			$faSystemErrors = $create;
		else if($create === TRUE)
			exit(header("Location: " . SITE_URI . "settings/notifications?NOTICE=Notification Sent"));
		else
			$faSystemErrors[] = "Could Not Send Notification";
	}
?>
<form class="basic-form form" method="post">
	<p>
		<span class="required">Title</span>
		<input type="text" name="title" value="<?=ifSet($_POST['title'])?>" maxlength="64">
	</p>
	<p>
		<span class="required">Message</span>
		<textarea name="data"><?=ifSet($_POST['data'])?></textarea>
	</p>
	<p>
		<span class="required">Important</span>
		<select name="important">
			<option value="no">No</option>
			<option value="yes"<?=ifSet($_POST['important']) == "yes" ? " selected" : ""?>>Yes</option>
		</select>
	</p>
	<p>
		<span>Recipients</span>
		<select name="roles[]" multiple size="5">
			<?php
				$roles = getRoles();
				foreach($roles as $role)
				{
					?>
					<option value="<?=$role->getId()?>"<?=(isset($_POST['roles']) AND in_array($role->getId(), $_POST['roles'])) ? " selected" : ""?>><?=$role->getName()?></option>
					<?php
				}
			?>
		</select>
	</p>
	<?php
		if(EMAIL_ENABLED === TRUE)
		{
		?>
		<p>
			<span>Send E-Mail?</span>
			<input type="checkbox" name="email" value="true"<?=isset($_POST['email']) ? " checked" : ""?>>
		</p>
		<?php
		}
	?>
	<input type="submit" class="button" value="Send" accesskey="s">
	<input type="button" class="button back-button" value="Cancel" accesskey="c">
</form>