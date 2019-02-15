<?php
	if(isset($_GET['submitForm']))
	{
		$validator = new Validator();
		
		if(isset($_GET['username']))
			$queryUsername = wildcard($_GET['username']);
		else
			$queryUsername = "%";
		
		if(isset($_GET['title']))
			$queryTitle = wildcard($_GET['title']);
		else
			$queryTitle = "%";
		
		if(isset($_GET['receiptDateStart']) AND $validator->validDate($_GET['receiptDateStart']))
			$queryReceiptDateStart = $_GET['receiptDateStart'];
		else
			$queryReceiptDateStart = "1000-01-01";
		
		if(isset($_GET['receiptDateEnd']) AND $validator->validDate($_GET['receiptDateEnd']))
			$queryReceiptDateEnd = $_GET['receiptDateEnd'];
		else
			$queryReceiptDateEnd = "9999-12-31";
		
		$inactiveFilter = [];
		if(isset($_GET['inactiveYes']))
			$inactiveFilter[] = 1;
		if(isset($_GET['inactiveNo']))
			$inactiveFilter[] = 0;
		
		$notifications = getNotifications($queryUsername, $queryTitle, $queryReceiptDateStart, $queryReceiptDateEnd);
		
		$results['type'] = "table";
		$results['head'] = ['Username', 'Title', 'Time', 'Important', 'Read', 'Deleted'];
		$results['data'] = [];
		$results['align'] = ["right", "left", "left", "left", "left", "left"];
		$results['widths'] = ["150px", "", "150px", "10px", "10px", "10px"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($notifications as $notification)
		{
			$user = new User($notification->getUser());
			$user->load();
			
			$results['data'][] = [$user->getUsername(), $notification->getTitle(), $notification->getTime(), ($notification->getImportant() == "1" ? "✔" : ""), ($notification->getRead() == "1" ? "✔" : ""), ($notification->getDeleted() == "1" ? "✔" : "")];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/send" accesskey="e">Send</a>
	
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitForm" value="true">
	<table class="table-display">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?=ifSet($_GET['username'])?>"></td>
			<td>Title</td>
			<td><input type="text" name="title" value="<?=ifSet($_GET['title'])?>"></td>
		</tr>
		<tr>
			<td>Date Start</td>
			<td><input class="date-input" name="receiptDateStart" type="text" autocomplete="off" value="<?=ifSet($_GET['receiptDateStart'])?>"></td>
			<td>Date End</td>
			<td><input class="date-input" name="receiptDateEnd" type="text" autocomplete="off" value="<?=ifSet($_GET['receiptDateEnd'])?>"></td>
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