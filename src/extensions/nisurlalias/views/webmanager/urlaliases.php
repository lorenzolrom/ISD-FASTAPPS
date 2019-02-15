<?php
	use nisurlalias as ua;
	
	if(isset($_GET['submitted']))
	{
		$aliases = ua\getAliases(wildcard($_GET['alias']), wildcard($_GET['destination']));
		
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/edit?a=";
		$results['head'] = ['Alias', 'Destination', 'Disabled'];
		$results['widths'] = ["150px", "", "10px"];
		$results['align'] = ["right", "left", "center"];
		$results['refs'] = [];
		$results['data'] = [];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($aliases as $alias)
		{
			$results['refs'][] = [$alias->getId()];
			$results['data'][] = [$alias->getAlias(), $alias->getDestination(), $alias->getDisabled() == 1 ? "✔" : ""];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new?new" accesskey="c">Create</a>
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Alias</td>
			<td><input type="text" name="alias" value="<?=ifSet($_GET['alias'])?>"></td>
			<td>Destination</td>
			<td><input type="text" name="destination" value="<?=ifSet($_GET['destination'])?>"></td>
		</tr>
		<tr>
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