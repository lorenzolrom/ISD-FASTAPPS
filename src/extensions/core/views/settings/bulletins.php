<?php
	if(isset($_GET['submitForm']))
	{
		$validator = new Validator();
		
		if(isset($_GET['title']))
			$queryTitle = wildcard($_GET['title']);
		else
			$queryTitle = "%";
		
		if(isset($_GET['startDate']) AND $validator->validDate($_GET['startDate']))
			$queryStartDate = $_GET['startDate'];
		else
			$queryStartDate = "1000-01-01";
		
		if(isset($_GET['endDate']) AND $validator->validDate($_GET['endDate']))
			$queryEndDate = $_GET['endDate'];
		else
			$queryEndDate = "9999-12-31";
		
		$inactiveFilter = [];
		if(isset($_GET['inactiveYes']))
			$inactiveFilter[] = 1;
		if(isset($_GET['inactiveNo']))
			$inactiveFilter[] = 0;
		
		$bulletins = getBulletins($queryStartDate, $queryEndDate, $queryTitle, $inactiveFilter);
		
		$results['type'] = "table";
		$results['linkColumn'] = 0; // Define the column to be used as the link
		$results['href'] = getURI() . "/edit?b="; // Define the link target
		$results['head'] = ['Title', 'Type', 'Start Date', 'End Date', 'Inactive'];
		$results['refs'] = [];
		$results['data'] = [];
		$results['align'] = ["left", "right", "right", "right", "center"];
		$results['widths'] = ["", "50px", "150px", "150px", "10px"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($bulletins as $bulletin)
		{
			$results['refs'][] = $bulletin->getId();
			$results['data'][] = [$bulletin->getTitle(), ($bulletin->getBulletinType() == 'a' ? "Alert" : "Info"), $bulletin->getStartDate(), 
				$bulletin->getEndDate(), ($bulletin->getInactive() == 1 ? "✔" : "")];
		}
	}
	else
	{
		$_GET['inactiveYes'] = "true";
		$_GET['inactiveNo'] = "true";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new?new" accesskey="c">Create</a>
	
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitForm" value="true">
	<table class="table-display">
		<tr>
			<td>Title</td>
			<td><input type="text" name="title" value="<?=ifSet($_GET['title'])?>"></td>
			<td>Inactive</td>
			<td>
				<input type="checkbox" value="true" name="inactiveYes"<?=ifSet($_GET['inactiveYes']) ? " checked" : ""?>><label>Yes</label>
				<input type="checkbox" value="true" name="inactiveNo"<?=ifSet($_GET['inactiveNo']) ? " checked" : ""?>><label>No</label>
			</td>
		</tr>
		<tr>
			<td>Date Start</td>
			<td><input class="date-input" name="startDate" type="text" autocomplete="off" value="<?=ifSet($_GET['startDate'])?>"></td>
			<td>Date End</td>
			<td><input class="date-input" name="endDate" type="text" autocomplete="off" value="<?=ifSet($_GET['endDate'])?>"></td>
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