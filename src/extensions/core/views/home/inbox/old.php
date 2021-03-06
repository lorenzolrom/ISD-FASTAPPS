<?php
	$results['type'] = "table";
	$results['linkColumn'] = 0;
	$results['href'] = getURI() . "/../view?n=";
	$results['head'] = ['Title', 'Time', 'Message'];
	$results['refs'] = [];
	$results['data'] = [];
	
	foreach($faCurrentUser->getNotifications() as $message)
	{
		// Ignore read notifications
		if($message->getRead() == 0)
			continue;
		
		$results['refs'][] = [$message->getId()];
		$results['data'][] = [$message->getTitle(), $message->getTime(), (strlen($message->getdata()) > 130 ? (substr($message->getData(), 0, 130) . "...") : $message->getData())];
	}
?>
<div class="button-bar">
	<span class="button back-button">Back</span>
</div>
<div id="results">
</div>
<?php
	if(isset($results))
	{
		?>
		<script>showResults('results', <?=json_encode($results)?>, <?=RESULTS_PER_PAGE?>)</script>
		<?php
	}
?>