<?php
	use itsmcore as itsmcore;
	
	$results['type'] = "table";
	$results['linkColumn'] = 0;
	$results['href'] = getURI() . "/view?v=";
	$results['head'] = ['Code', 'Name', 'City', 'State'];
	$results['refs'] = [];
	$results['data'] = [];
	$results['align']= ["right", "left", "left", "left"];
	$results['widths'] = ["150px", "", "150px", "10px"];
	
	foreach(itsmcore\getVendors() as $vendor)
	{
		$results['data'][] = [$vendor->getCode(), $vendor->getName(), $vendor->getCity(), $vendor->getState()];
		$results['refs'][] = [$vendor->getId()];
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