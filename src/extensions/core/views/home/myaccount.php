<div class="button-bar">
	<a class="button" href="<?=getURI()?>/editdetails">Change Account Details</a>
	<a class="button" href="<?=getURI()?>/changepassword">Change Password</a>
</div>
<div class="profile">
	<h3><?=$faCurrentUser->getLastName() . ", " . $faCurrentUser->getFirstName()?></h3>
	<?php
		if(!empty($faCurrentUser->getEmail()))
		{
		?>
			<p><?=$faCurrentUser->getEmail()?></p>
		<?php
		}
	?>
	<span>(<?=((LDAP_ENABLED === TRUE AND $faCurrentUser->getAuthType() == "ldap") ? (LDAP_DOMAIN . "\\") : "") . $faCurrentUser->getUsername()?>)</span>
	<p>Your Roles</p>
	<ul>
		<?php
			foreach($faCurrentUser->getRoles() as $role)
			{
				?>
				<li><?=htmlentities($role->getName())?></li>
				<?php
			}
		?>
	</ul>
</div>