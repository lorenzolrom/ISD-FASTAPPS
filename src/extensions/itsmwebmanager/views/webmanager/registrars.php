<?php
	use itsmwebmanager as itsmwebmanager;
	
	if(isset($_GET['submitted']))
	{
		$registrars = itsmwebmanager\getRegistrars(wildcard(ifSet($_GET['code'])), wildcard(ifSet($_GET['name'])));
		
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?r=";
		$results['head'] = ['Code', 'Name'];
		$results['align'] = ["center", "left"];
		$results['widths'] = ["10px", ""];
		$results['refs'] = [];
		$results['data'] = [];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($registrars as $registrar)
		{
			$results['refs'][] = [$registrar->getId()];
			$results['data'][] = [$registrar->getCode(), $registrar->getName()];
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