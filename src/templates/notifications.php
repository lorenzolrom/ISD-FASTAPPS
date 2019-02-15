<div id="notifications">	
	<div id="notifications-dismiss">X</div>
	<?php
		if(!empty($faSystemErrors))
		{
			$faShowNotification = TRUE;
			
			?><h3>Error</h3><ul><?php
			foreach($faSystemErrors as $errorMessage)
			{
				?><li>- <?=$errorMessage?></li><?php
			}
			?></ul><script>$('#notifications').addClass('notifications-error');</script><?php
		}
		else if(isset($faSystemNotification) OR isset($_GET['NOTICE']))
		{
			$faShowNotification = TRUE;
			?><h3>Notice</h3>
			<p><?=ifSet($faSystemNotification) !== FALSE ? $faSystemNotification : $_GET['NOTICE']?></p>
			<script>$('#notifications').addClass('notifications-notice');</script>
			<?php
		}
		
		if(isset($faShowNotification))
		{
			?><script>$('#notifications').fadeIn();</script><?php
		}
	?>
</div>