<?php
	// Process form submission
	if(!empty($_POST))
	{
		$save = $faCurrentUser->updateDetails($_POST);
		
		if(is_array($save))
			$faSystemErrors = $save;
		else if($save === TRUE)
			exit(header("Location: " . SITE_URI . "home/myaccount?NOTICE=Changes Saved")); // Redirect to account page
		else
			$faSystemErrors[] = "Could Not Update Account Details";
	}

	// Export current attributes into POST array
	$_POST['firstName'] = $faCurrentUser->getFirstName();
	$_POST['lastName'] = $faCurrentUser->getLastName();
	
	if($faCurrentUser->getAuthType() == "ldap")
		$faSystemErrors[] = "LDAP Account Settings Cannot Be Changed On This Page";
?>
	<form class="basic-form form" method="post">
		<?php
		if($faCurrentUser->getAuthType() == "loca")
		{
			?>
			<p>
				<span class="required">First Name</span>
				<input type="text" name="firstName" maxlength=30 value="<?=htmlentities(ifSet($_POST['firstName']))?>">
			</p>
			<p>
				<span class="required">Last Name</span>
				<input type="text" name="lastName" maxlength=30 value="<?=htmlentities(ifSet($_POST['lastName']))?>">
			</p>
			<p>
				<span>E-Mail</span>
				<input type="text" name="email" value="<?=htmlentities(ifSet($_POST['email']))?>">
			</p>
			<input type="submit" class="button" value="Save">
			<?php
		}
		?>
		<input type="button" class="button back-button" value="Cancel">
	</form>