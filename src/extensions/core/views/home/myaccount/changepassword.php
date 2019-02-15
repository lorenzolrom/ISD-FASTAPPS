<?php
	if(!empty($_POST))
	{
		$change = $faCurrentUser->changePassword($_POST);
		
		if(is_array($change))
			$faSystemErrors = $change;
		else if($change === TRUE)
			exit(header("Location: " . SITE_URI . "home/myaccount?NOTICE=Password Updated"));// Redirect to account page
		else
			$faSystemErrors[] = "Could Not Change Password";
	}
?>
<form class="basic-form form" method="post">
	<p>
		<span class="required">Current Password</span>
		<input type="password" name="password">
	</p>
	<p>
		<span class="required">New Password</span>
		<input type="password" name="new">
	</p>
	<p>
		<span class="required">Confirm Password</span>
		<input type="password" name="confirm">
	</p>
	<input type="submit" class="button" value="Save">
	<input type="button" class="button back-button" value="Cancel">
</form>