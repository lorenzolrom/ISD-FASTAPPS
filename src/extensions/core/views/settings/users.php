<?php
	if(isset($_GET['submitted']))
	{
		$disabledFilter = [];
		if(isset($_GET['disabledYes']))
			$disabledFilter[] = 1;
		if(isset($_GET['disabledNo']))
			$disabledFilter[] = 0;
		
		$users = getUsers($disabledFilter, wildcard(ifSet($_GET['username'])), wildcard(ifSet($_GET['firstName'])), wildcard(ifSet($_GET['lastName'])));
		
		// Build results array
		$results['type'] = "table"; // Define the type of result
		$results['linkColumn'] = 0; // Define the column to be used as the link
		$results['href'] = getURI() . "/edit?u="; // Define the link target
		$results['head'] = ['Username', 'First Name', 'Last Name', 'E-Mail', 'Disabled', 'Authentication']; // Results header
		$results['refs'] = []; // Blank array for target reference
		$results['data'] = []; // Blank array for data
		$results['align'] = ["center", "left", "left", "left",  "center", "left"];
		$results['widths'] = ["150px", "", "", "", "10px", "50px"];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($users as $resultUser)
		{
			$results['refs'][] = [$resultUser->getId()];
			$results['data'][] = [$resultUser->getUsername(), $resultUser->getFirstName(), $resultUser->getLastName(), $resultUser->getEmail(), ($resultUser->getDisabled()) ? "✔" : "", ($resultUser->getAuthType() == "ldap") ? "LDAP" : "Local"];
		}
	}
	else
	{
		$_GET['disabledYes'] = "true";
		$_GET['disabledNo'] = "true";
	}
?>
<div class="button-bar">
	<span class="button table-form form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/add" accesskey="a">Add User</a>
</div>
<form class="search-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?=ifSet($_GET['username'])?>"></td>
			<td>Disabled</td>
			<td><input type="checkbox" value="true" name="disabledYes"<?=ifSet($_GET['disabledYes']) ? " checked" : ""?>><label>Yes</label> <input type="checkbox" value="true" name="disabledNo"<?=ifSet($_GET['disabledNo']) ? " checked" : ""?>><label>No</label></td>
		</tr>
		<tr>
			<td>First Name</td>
			<td><input type="text" name="firstName" value="<?=ifSet($_GET['firstName'])?>"></td>
			<td>Last Name</td>
			<td><input type="text" name="lastName" value="<?=ifSet($_GET['lastName'])?>"></td>
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