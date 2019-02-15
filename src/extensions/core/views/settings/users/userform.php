<form class="basic-form form" method="post">
	<p>
		<span class="required">Username</span>
		<input type="text" name="username" maxlength=64 value="<?=htmlentities(ifSet($_POST['username']))?>">
	</p>
	<?php
		if(LDAP_ENABLED === TRUE)
		{
		?>
		<p>
			<span class="required">Authentication</span>
			<select id="authSelector" name="authType">
				<option value="loca">Local</option>
				<option value="ldap"<?=(ifSet($_POST['authType']) == "ldap") ? " selected" : ""?>>LDAP</option>
			</select>
		</p>
		<?php
		}
		else
		{
		?>
			<input type="hidden" name="authType" value="loca">
		<?php
		}
	?>
	<p>
		<span class="required">First Name</span>
		<input id="firstName" type="text" name="firstName" maxlength=30 value="<?=htmlentities(ifSet($_POST['firstName']))?>">
	</p>
	<p>
		<span class="required">Last Name</span>
		<input id="lastName" type="text" name="lastName" maxlength=30 value="<?=htmlentities(ifSet($_POST['lastName']))?>">
	</p>
	<p>
		<span>E-Mail</span>
		<input id="email" type="text" name="email" value="<?=htmlentities(ifSet($_POST['email']))?>">
	</p>
	<p>
		<span>Roles</span>
		<select name="roles[]" multiple>
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
	<p>
		<span>Password</span>
		<input id="password" type="password" name="password">
	</p>
	<p>
		<span>Confirm Password</span>
		<input id="confirm" type="password" name="confirm">
	</p>
	<p>
		<span class="required">Status</span>
		<select name="disabled">
			<option value=0>Enabled</option>
			<option value=1 <?=(ifSet($_POST['disabled']) == 1) ? " selected" : ""?>>Disabled</option>
		</select>
	</p>
	<input type="submit" class="button" value="Save" accesskey="s">
	<input type="button" class="button back-button" value="Cancel" accesskey="c">
</form>
<script>
	$(document).ready(function(){
		userform_checkStatus();
		
		// When authentication type is changed, update the form
		$('#authSelector').change(function(){userform_checkStatus()});
	});
	
	// Check for authentication type and update form fields
	function userform_checkStatus()
	{
		var selector = $('#authSelector');
		var firstNameInput = $('#firstName');
		var lastNameInput = $('#lastName');
		var passwordInput = $('#password');
		var confirmInput = $('#confirm');
		var emailInput = $('#email');
		
		var selected = $(selector).val();
		
		// If authentication type is LDAP
		if(selected == "ldap")
		{
			// Clear name inputs
			$(firstNameInput).val("");
			$(lastNameInput).val("");
			$(emailInput).val("");
			
			// Disable name inputs
			$(firstNameInput).attr("disabled", "disabled");
			$(lastNameInput).attr("disabled", "disabled");
			$(emailInput).attr("disabled", "disabled");
			
			// Remove password inputs
			$(passwordInput).parent().hide();
			$(confirmInput).parent().hide();
		}
		else
		{
			// Enable name inputs
			$(firstNameInput).removeAttr("disabled");
			$(lastNameInput).removeAttr("disabled");
			$(emailInput).removeAttr("disabled");
			
			// Show password inputs
			$(passwordInput).parent().show();
			$(confirmInput).parent().show();
		}
	}
</script>