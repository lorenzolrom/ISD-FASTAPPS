<?php
	use itsmcore as itsmcore;
	
	$results['type'] = "table";
	$results['linkColumn'] = 0;
	$results['href'] = getURI() . "/edit?t=";
	$results['head'] = ['Code', 'Name'];
	$results['refs'] = [];
	$results['data'] = [];
	$results['widths'] = ["25px", ""];
	$results['align'] = ["right", "left"];
	
	foreach(getAttributes('itsm', 'asty') as $type)
	{
		$results['data'][] = [$type->getCode(), $type->getName()];
		$results['refs'][] = [$type->getId()];
	}
?>
<div class="button-bar">
	<a class="button" href="<?=getURI()?>/new" accesskey="c">Create</a>
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