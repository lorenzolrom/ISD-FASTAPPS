<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="<?=URI_STYLE?>Core.css">
		<link rel="stylesheet" type="text/css" href="<?=URI_SCRIPT?>jquery-ui-1.12.1/jquery-ui.min.css">
		<script src="<?=URI_SCRIPT?>jquery-3.3.1.min.js"></script>
		<script src="<?=URI_SCRIPT?>jquery-ui-1.12.1/jquery-ui.min.js"></script>
		<script src="<?=URI_SCRIPT?>core.js"></script>
		<?php
			// If any themes are enabled
			if(!empty($ENABLED_THEMES))
			{
				foreach($ENABLED_THEMES as $theme)
				{
					$stylePath = PATH_THEME . $theme . "/stylesheets";
					$scriptPath = PATH_THEME . $theme . "/scripts";
					
					// Check for stylesheets
					if(is_dir($stylePath))
					{
						foreach(scandir($stylePath) as $stylesheet)
						{
							if($stylesheet == "." OR $stylesheet == "..")
								continue;
							
							?>
							<link rel="stylesheet" type="text/css" href="<?=URI_THEME . $theme . "/stylesheets/" . $stylesheet?>">
							<?php
						}
					}
					
					// Check for scripts
					if(is_dir($scriptPath))
					{
						foreach(scandir($scriptPath) as $script)
						{
							if($script == "." OR $script == "..")
								continue;
							
							?>
							<script src="<?=URI_THEME . $theme . "/scripts/" . $script?>"></script>
							<?php
						}
					}
				}
			}
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?=$faCurrentPageTitle . " | " . SITE_TITLE?></title>
	</head>
	<body>
		<div id="app-window">
			<?php
			if(!isset($_GET['POPUP']))
				require_once(dirname(__FILE__) . "/header.php");
			else
				require_once(dirname(__FILE__) . "/header-popup.php");
			?>
			<div id="app-container">
				<?php
					if(!isset($_GET['POPUP']) AND isset($faCurrentSection))
						require_once(dirname(__FILE__) . "/sidebar.php");
				?>
				<div id="view">
					<div id="veil">
						<img src="<?=URI_MEDIA?>animations/wait.gif" alt="">
					</div>
					<?php
						if(!isset($_GET['POPUP']))
						{
						?>
							<ul id="breadcrumbs">
								<?php
									if($faCurrentPage !== NULL)
									{						
										$parentStack = $faCurrentPage->getParentStack();
										for($i = 0; $i < sizeof($parentStack) - 1; $i++)
										{
											$parent = $parentStack[$i];
											?>
											<li><a href="<?=SITE_URI . $parent->getURL()?>"><?=$parent->getTitle()?></a></li>
											<?php
										}
										?>
										<li><?=$parentStack[sizeof($parentStack) - 1]->getTitle()?></li>
										<?php
									}
								?>
							</ul>
						<?php
						}
					?>
					<h1><?=$faCurrentPageTitle?></h1>