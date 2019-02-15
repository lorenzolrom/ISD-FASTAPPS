<?php
	use facilitiesfloorplans as fp;
	use facilitiescore as fc;
	
	if(isset($_GET['submitted']))
	{
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		
		$floorplans = fp\getFloorplans(wildcard(ifSet($_GET['buildingCode'])), wildcard(ifSet($_GET['floor'])));
		
		$results['type'] = "table";
		$results['linkColumn'] = 1;
		$results['href'] = getURI() . "/view?f=";
		$results['head'] = ['Building', 'Floor'];
		$results['widths'] = ["150px", ""];
		$results['align'] = ["right", "left"];
		$results['refs'] = [];
		$results['data'] = [];
		
		foreach($floorplans as $floorplan)
		{
			$building = new fc\Building($floorplan->getBuilding());
			$building->load();
			
			$results['refs'][] = [$floorplan->getId()];
			$results['data'][] = [$building->getCode(), $floorplan->getFloor()];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new?new" accesskey="c">Create</a>
</div>
<form id="search-form" class="search-form table-form form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Building</td>
			<td><input type="text" name="buildingCode" value="<?=ifSet($_GET['buidingCode'])?>"></td>
			<td>Floor</td>
			<td><input type="text" name="floor" value="<?=ifSet($_GET['floor'])?>"></td>
		</tr>
		<tr>
			<td>Results Per Page</td>
			<td><input class="results-per-page tiny-input" maxlength=3 type="text" name="resultsPerPage" value="<?=ifSet($_GET['resultsPerPage'])?>"></td>
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