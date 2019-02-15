<div id="sidebar">
	<h2><?=$faCurrentSection->getTitle()?></h2>
	<ul>
	<?php
		foreach($faCurrentSection->getChildren(TRUE) as $navPage)
		{
			if($faCurrentUser->hasPermission($navPage->getPermission()))
			{
				?>
				<li><a href="<?=SITE_URI . $navPage->getURL()?>" class="<?=($navPage->getId() == $faCurrentPage->getId()) ? "navigation-current" : ""?>"><?=$navPage->getTitle()?></a></li>
				<?php
			}
		}
	?>
	</ul>
</div>