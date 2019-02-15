<form class="basic-form form" method="post">
	<p>
		<span class="required">Name</span>
		<input type="text" name="name" maxlength=64 value="<?=htmlentities(ifSet($_POST['name']))?>">
	</p>
	<p>
		<span>Permissions</span>
		<select name="permissions[]" multiple>
		<?php
			$permissions = getPermissions();
			foreach($permissions as $permission)
			{
				?>
				<option value="<?=$permission?>"<?=(isset($_POST['permissions']) AND in_array($permission, $_POST['permissions'])) ? " selected" : ""?>><?=$permission?></option>
				<?php
			}
		?>
		</select>
	</p>
	<input type="submit" class="button" value="Save" accesskey="s">
	<input type="button" class="button back-button" value="Cancel" accesskey="c">
</form>