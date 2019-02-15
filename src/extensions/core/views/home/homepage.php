<div class="bulletin-list">
	<?php
		// Fetch any active bulletins for this user, and display them
		foreach(getUserActiveBulletins() as $bulletin)
		{
			?>
			<div class="bulletin">
				<h2 class="bulletin-title-<?=$bulletin->getBulletinType() == 'a' ? "alert" : "info"?>">
					<?php
						if(ICONS_ENABLED)
						{
							?><img src="<?=URI_ICON . ($bulletin->getBulletinType() == 'a' ? "error.png" : "about.png")?>" alt=""><?php
						}
					?>
					<?=$bulletin->getTitle()?>
				</h2>
				<div><?=$bulletin->getMessage()?></div>
			</div>
			<?php
		}
	?>
</div>