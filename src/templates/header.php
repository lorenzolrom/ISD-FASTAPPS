<div id="header">
	<div id="account">
		<div id="account-user">
			<span><?=$faCurrentUser->getFirstName() . " " . $faCurrentUser->getLastName()?></span>
			<ul>
				<li><a href="<?=SITE_URI?>home/myaccount">My Account</a></li>
				<li><a href="<?=SITE_URI?>login.php?logout=yes">Logout</a></li>
			</ul>
		</div>
		<a href="<?=SITE_URI?>home/inbox" id="account-notifications">
            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 width="500px" height="328.125px" viewBox="0 0 500 328.125" enable-background="new 0 0 500 328.125" xml:space="preserve">
                <g>
                    <path id="mail" d="M437.5,0h-375C27.984,0,0,28,0,62.5v203.125c0,34.518,27.984,62.5,62.5,62.5h375c34.516,0,62.5-27.982,62.5-62.5
                        V62.5C500,28,472.016,0,437.5,0z M31.25,82.031l109.359,82.031L31.25,246.094V82.031z M468.75,265.625
                        c0,17.25-14.031,31.25-31.25,31.25h-375c-17.234,0-31.25-14-31.25-31.25l122.375-91.797l68.25,51.203
                        c8.328,6.219,18.219,9.375,28.125,9.375c9.891,0,19.781-3.141,28.109-9.375l68.266-51.203L468.75,265.625L468.75,265.625z
                         M468.75,246.094l-109.375-82.031L468.75,82.031V246.094z M268.734,212.531c-5.453,4.094-11.922,6.25-18.734,6.25
                        c-6.812,0-13.297-2.172-18.75-6.25l-64.609-48.469l-13.016-9.766L31.25,62.516V62.5c0-17.234,14.016-31.25,31.25-31.25h375
                        c17.219,0,31.25,14.016,31.25,31.25L268.734,212.531z"/>
                </g>
            </svg>
			<?php
				$notificationCount = $faCurrentUser->getUnreadNotificationCount();
				if($notificationCount > 0)
				{
					?>
					<span><?=$notificationCount?></span>
					<?php
				}
			?>
		</a>
	</div>
	<span id="left-logo"></span>
	<span id="logo"></span>
</div>
<ul id="navigation">
<?php
	$navSections = getSections(TRUE);
	
	foreach($navSections as $navSection)
	{
		if($faCurrentUser->hasPermission($navSection->getPermission()))
		{
			?>
			<li><a href="<?=SITE_URI . $navSection->getURL()?>" class="<?=(isset($faCurrentSection) AND $navSection->getId() == $faCurrentSection->getId()) ? "navigation-current" : ""?>">
					<?php
						// Should icons be added to nav?
						if(ICONS_ENABLED)
						{
							// If icon is defined
							if($navSection->getIcon() !== NULL)
							{
								?><img src="<?=URI_ICON . $navSection->getIcon()?>" alt=""><?php
							}
						}
						echo $navSection->getTitle();
					?>
				</a>
			<?php
			if(isset($faCurrentSection) AND $navSection->getId() != $faCurrentSection->getId())
			{		
				?>
				<ul>
				<?php
				foreach($navSection->getChildren(TRUE) as $navPage)
				{
					if($faCurrentUser->hasPermission($navPage->getPermission()))
					{
						?>
						<li><a href="<?=SITE_URI . $navPage->getURL()?>"><?=$navPage->getTitle()?></a></li>
						<?php
					}
				}
				?>
				</ul>
				<?php
			}
			?>
			</li>
			<?php
		}
	}
?>
</ul>