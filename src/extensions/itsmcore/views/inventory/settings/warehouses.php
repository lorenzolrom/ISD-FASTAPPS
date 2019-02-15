<?php
	use itsmcore as itsmcore;
	
	$results['type'] = "table";
	$results['linkColumn'] = 0;
	$results['href'] = getURI() . "/view?w=";
	$results['head'] = ['Code', 'Name'];
	$results['refs'] = [];
	$results['data'] = [];
	$results['widths'] = ["150px", ""];
	$results['align'] = ["right", "left"];
	
	foreach(itsmcore\getWarehouses(TRUE) as $warehouse)
	{
		$results['data'][] = [$warehouse->getCode(), $warehouse->getName()];
		$results['refs'][] = [$warehouse->getId()];
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