<?php
	use itsmservicemonitor as sm;
	
	$results['type'] = "table";
	$results['linkColumn'] = 0;
	$results['href'] = getURI() . "/edit?c=";
	$results['head'] = ['Name', 'Displayed'];
	$results['widths'] = ["", "10px"];
	$results['refs'] = [];
	$results['data'] = [];
	
	foreach(sm\getCategories() as $category)
	{
		$results['refs'][] = [$category->getId()];
		$results['data'][] = [$category->getName(), $category->getDisplayed() == "1" ? "✔" : ""];
	}
?>
<div class="button-bar">
	<a class="button" href="<?=SITE_URI?>monitor/services/categories/new?new">Create</a>
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