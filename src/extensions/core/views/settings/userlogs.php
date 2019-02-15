<?php
	if(isset($_GET['submitForm']))
	{
		$validator = new Validator();
		
		if(isset($_GET['username']))
			$queryUsername = wildcard($_GET['username']);
		else
			$queryUsername = "%";
		
		if(isset($_GET['ipAddress']))
			$queryIpAddress = wildcard($_GET['ipAddress']);
		else
			$queryIpAddress = "%";
		
		if(isset($_GET['logInDateStart']) AND $validator->validDate($_GET['logInDateStart']))
			$queryLogInDateStart = $_GET['logInDateStart'];
		else
			$queryLogInDateStart = "1000-01-01";
		
		if(isset($_GET['logInDateEnd']) AND $validator->validDate($_GET['logInDateEnd']))
			$queryLogInDateEnd = $_GET['logInDateEnd'];
		else
			$queryLogInDateEnd = "9999-12-31";
		
		$tokens = getTokens($queryUsername, $queryIpAddress, $queryLogInDateStart, $queryLogInDateEnd);
		
		$results['type'] = "table";
		$results['head'] = ["Username", "IP Address", "Login Time", "Expire Time", "Expired"];
		$results['data'] = [];
		$results['align'] = ["right", "left", "left", "left", "center"];
		$results['widths'] = ["150px", "150px", "", "", "10px"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($tokens as $token)
		{
			$user = new User($token->getUser());
			$user->load();
			
			$results['data'][] = [$user->getUsername(), $token->getIpAddress(), $token->getIssueTime(), $token->getExpireTime(), ($token->getExpired() == "1" ? "✔" : "")];
		}
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
</div>
<form class="search-form table-form form" id="search-form">
	<input type="hidden" name="submitForm" value="true">
	<table class="table-display">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?=ifSet($_GET['username'])?>"></td>
			<td>IP Address</td>
			<td><input type="text" name="ipAddress" value="<?=ifSet($_GET['ipAddress'])?>"></td>
		</tr>
		<tr>
			<td>Date Start</td>
			<td><input class="date-input" name="logInDateStart" type="text" autocomplete="off" value="<?=ifSet($_GET['logInDateStart'])?>"></td>
			<td>Date End</td>
			<td><input class="date-input" name="logInDateEnd" type="text" autocomplete="off" value="<?=ifSet($_GET['logInDateEnd'])?>"></td>
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