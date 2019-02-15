<?php
	use itsmservices as itsmservices;
	
	if(!isset($_GET['type']) OR !is_array($_GET['type']))
		$_GET['type'] = [];
	if(!isset($_GET['status']) OR !is_array($_GET['status']))
		$_GET['status'] = [];
	if(!isset($_GET['authType']) OR !is_array($_GET['authType']))
		$_GET['authType'] = [];
	if(!isset($_GET['dataVolume']) OR !is_array($_GET['dataVolume']))
		$_GET['dataVolume'] = [];
	if(!isset($_GET['lifeExpectancy']) OR !is_array($_GET['lifeExpectancy']))
		$_GET['lifeExpectancy'] = [];
	
	$publicFacing = [];
	
	if(isset($_GET['publicFacingYes']))
		$publicFacing[] = 1;
	if(isset($_GET['publicFacingNo']))
		$publicFacing[] = 0;
	
	if(isset($_GET['submitted']))
	{
		$applications = itsmservices\getApplications(wildcard(ifSet($_GET['number'])), wildcard(ifSet($_GET['name'])), 
			wildcard(ifSet($_GET['description'])), wildcard(ifSet($_GET['ownerUsername'])), $_GET['type'], 
			$publicFacing, $_GET['lifeExpectancy'], $_GET['dataVolume'], $_GET['authType'], 
			wildcard(ifSet($_GET['port'])), wildcard(ifSet($_GET['host'])), wildcard(ifSet($_GET['vhost'])), 
			$_GET['status']);
		
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?a=";
		$results['head'] = ['Number', 'Name', 'Type', 'Status', 'Owner'];
		$results['widths'] = ["10px", "" , "150px", "150px", "200px"];
		$results['align'] = ["center", "left", "left", "left", "right"];
		$results['refs'] = [];
		$results['data'] = [];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($applications as $application)
		{
			$status = new Attribute($application->getStatus());
			$status->load();
			$type = new Attribute($application->getApplicationType());
			$type->load();
			$owner = new User($application->getOwner());
			$owner->load();
			
			$results['refs'][] = [$application->getNumber()];
			$results['data'][] = [$application->getNumber(), $application->getName(), $type->getName(), $status->getName(), ($owner->getFirstName() . " " . $owner->getLastName() . " (" . $owner->getUsername() . ")")];
		}
	}
	else
	{
		$_GET['publicFacingYes'] = "true";
		$_GET['publicFacingNo'] = "true";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=getURI()?>/new?new" accesskey="c">Create</a>
</div>
<form class="search-form table-form large-search-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Number</td>
			<td><input type="text" name="number" value="<?=ifSet($_GET['number'])?>"></td>
			<td>Name</td>
			<td><input type="text" name="name" value="<?=ifSet($_GET['name'])?>"></td>
		</tr>
		<tr>
			<td>Description</td>
			<td><input type="text" name="description" value="<?=ifSet($_GET['description'])?>"></td>
			<td>Type</td>
			<td>
				<select name="type[]" multiple size=3>
				<?php
					foreach(getAttributes('itsm', 'aitt') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['type']) ? " selected" : "")?>><?=$attribute->getName()?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>App Owner</td>
				<td><input type="text" name="ownerUsername" value="<?=ifSet($_GET['ownerUsername'])?>"></td>
				<td>V. Host</td>
				<td><input type="text" name="vhost" value="<?=ifSet($_GET['vhost'])?>"></td>
			</tr>
			<tr>
				<td>Host</td>
				<td><input type="text" name="host" value="<?=ifSet($_GET['host'])?>"></td>
				<td>Port</td>
				<td><input type="text" name="port" value="<?=ifSet($_GET['port'])?>"></td>
			</tr>
			<tr>
				<td>Life Expectancy</td>
				<td>
					<select name="lifeExpectancy[]" multiple size=3>
					<?php
						foreach(getAttributes('itsm', 'aitl') as $attribute)
						{
							?>
							<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['lifeExpectancy']) ? " selected" : "")?>><?=$attribute->getName()?></option>
							<?php
						}
					?>
					</select>
				</td>
				<td>Data Volume</td>
				<td>
					<select name="dataVolume[]" multiple size=3>
					<?php
						foreach(getAttributes('itsm', 'aitd') as $attribute)
						{
							?>
							<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['dataVolume']) ? " selected" : "")?>><?=$attribute->getName()?></option>
							<?php
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Auth. Type</td>
				<td>
					<select name="authType[]" multiple size=3>
					<?php
						foreach(getAttributes('itsm', 'aita') as $attribute)
						{
							?>
							<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['authType']) ? " selected" : "")?>><?=$attribute->getName()?></option>
							<?php
						}
					?>
					</select>
				</td>
				<td>Status</td>
				<td>
					<select name="status[]" multiple size=3>
					<?php
						foreach(getAttributes('itsm', 'aits') as $attribute)
						{
							?>
							<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['status']) ? " selected" : "")?>><?=$attribute->getName()?></option>
							<?php
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Public Facing</td>
				<td><input type="checkbox" value="true" name="publicFacingYes"<?=(isset($_GET['publicFacingYes']) ? " checked" : "")?>><label>Yes</label> <input type="checkbox" value="true" name="publicFacingNo"<?=(isset($_GET['publicFacingNo']) ? " checked" : "")?>><label>No</label></td>
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