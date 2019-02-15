<?php
	if(isset($_GET['submitted']))
	{
		
		$roles = getRoles(wildcard(ifSet($_GET['name'])));
		
		// Build results array
		$results['type'] = "table"; // Define the type of result
		$results['linkColumn'] = 0; // Define the column to be used as the link
		$results['href'] = getURI() . "/edit?r="; // Define the link target
		$results['head'] = ['Name']; // Results header
		$results['refs'] = []; // Blank array for target reference
		$results['data'] = []; // Blank array for data
		$results['align'] = ["left"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($roles as $role)
		{
			$results['refs'][] = [$role->getId()];
			$results['data'][] = [$role->getName()];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/add" accesskey="a">Add Role</a>
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
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