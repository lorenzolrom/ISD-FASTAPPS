<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['submitted']))
	{
		$locations = facilitiescore\getLocations(wildcard(ifSet($_GET['building'])), wildcard(ifSet($_GET['code'])), wildcard(ifSet($_GET['name'])), [0]);
		
		// Build results array
		$results['type'] = "table";
		$results['linkColumn'] = 1;
		$results['href'] = getURI() . "/view?l=";
		$results['head'] = ['Building', 'Code', 'Name'];
		$results['refs'] = [];
		$results['data'] = [];
		$results['align'] = ["right", "center", "left"];
		$results['widths'] = ["10px", "10px", ""];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($locations as $location)
		{
			$building = new facilitiescore\Building($location->getBuilding());
			$building->load();
			
			$results['refs'][] = [$location->getId()];
			$results['data'][] = [$building->getCode(), $location->getCode(), $location->getName()];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new" accesskey="c">Create</a>
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Building</td>
			<td><input type="text" name="building" value="<?=ifSet($_GET['building'])?>"></td>
			<td>Code</td>
			<td><input type="text" name="code" value="<?=ifSet($_GET['code'])?>"></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" name="name" value="<?=ifSet($_GET['name'])?>"></td>
			<td>Results Per Page</td>
			<td><input maxlength=3 class="results-per-page tiny-input" type="text" name="resultsPerPage" value="<?=ifSet($_GET['resultsPerPage'])?>"></td>
		</tr>
	</table>
</form>
<div id="results">
</div>
<?php
	if(isset($results))
	{
		?>
		<script>showResults('results', <?=json_encode($results)?>, <?=$resultsPerPage?>)</script>
		<?php
	}
?>