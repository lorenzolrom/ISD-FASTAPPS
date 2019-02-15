<p><?=SITE_TITLE?> running FASTAPPS Core version: <em><?=FA_VERSION?></em></p>
<h2>Enabled Extensions:</h2>
<ul>
<?php
	foreach($ENABLED_EXTENSIONS as $extension)
	{
		?>
		<li><?=$extension?></li>
		<?php
	}
?>
</ul>