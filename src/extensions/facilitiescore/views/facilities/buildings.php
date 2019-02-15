<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['submitted']))
	{
		$buildings = facilitiescore\getBuildings(wildcard(ifSet($_GET['code'])), wildcard(ifSet($_GET['name'])), wildcard(ifSet($_GET['streetAddress'])), wildcard(ifSet($_GET['city'])), wildcard(ifSet($_GET['state'])), wildcard(ifSet($_GET['zipCode'])), [0]);
		
		// Build results array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?b=";
		$results['head'] = ['Code', 'Name', 'Street Address', 'City', 'State', 'Zip Code'];
		$results['refs'] = [];
		$results['data'] = [];
		$results['align'] = ["center", "left", "left", "left", "left", "left"];
		$results['widths'] = ["10px", "", "", "150px", "10px", "150px"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($buildings as $building)
		{
			$results['refs'][] = [$building->getId()];
			$results['data'][] = [$building->getCode(), $building->getName(), 
				$building->getStreetAddress(), $building->getCity(), 
				$building->getState(), $building->getZipCode()];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new" accesskey="c">Create</a>
</div>
<form class="search-form table-form large-search-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Code</td>
			<td><input type="text" name="code" value="<?=ifSet($_GET['code'])?>"></td>
			<td>Name</td>
			<td><input type="text" name="name" value="<?=ifSet($_GET['name'])?>"></td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>Street Address</td>
				<td><input type="text" name="streetAddress" value="<?=ifSet($_GET['streetAddress'])?>"></td>
				<td>City</td>
				<td><input type="text" name="city" value="<?=ifSet($_GET['city'])?>"></td>
			</tr>
			<tr>
				<td>State</td>
				<td><input type="text" name="state" value="<?=ifSet($_GET['state'])?>"></td>
				<td>Zip Code</td>
				<td><input type="text" name="zipCode" value="<?=ifSet($_GET['zipCode'])?>"></td>
			</tr>
			<tr>
				<td>Results Per Page</td>
				<td><input maxlength=3 class="results-per-page tiny-input" type="text" name="resultsPerPage" value="<?=ifSet($_GET['resultsPerPage'])?>"></td>
			</tr>
		</tbody>
	</table>
	<div class="button-bar">
		<span class="button-noveil search-additional-field-toggle">Show More</span>
	</div>
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