<?php
	use itsmwebmanager as itsmwebmanager;
	use itsmcore as itsmcore;
	
	if(!isset($_GET['status']) OR !is_array($_GET['status']))
		$_GET['status'] = [];
	
	if(isset($_GET['submitted']))
	{
		$vhosts = itsmwebmanager\getVHosts(wildcard(ifSet($_GET['domain'])), wildcard(ifSet($_GET['subdomain'])), 
			wildcard(ifSet($_GET['name'])), wildcard(ifSet($_GET['hostAsset'])), wildcard(ifSet($_GET['registrar'])), $_GET['status']);
			
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?v=";
		$results['head'] = ['Sub-Domain', 'Domain', 'Registrar', 'Name', 'Status', 'Host'];
		$results['align'] = ["right", "left", "left", "left", "left", "left"];
		$results['widths'] = ["150px", "150px", "150px", "", "150px", ""];
		$results['refs'] = [];
		$results['data'] = [];
		$results['rowClasses'] = [];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($vhosts as $vhost)
		{
			$registrar = new itsmwebmanager\Registrar($vhost->getRegistrar());
			$registrar->load();
			
			$status = new Attribute($vhost->getStatus());
			$status->load();
			
			$host = new itsmcore\Host($vhost->getHost());
			$host->load();
			
			$rowClass = "itsm-vhost-dorm";
			
			if($status->getCode() == "acti")
				$rowClass = "itsm-vhost-acti";
			
			if($status->getCode() == "redi")
				$rowClass = "itsm-vhost-redi";
			
			if($status->getCode() == "expi")
				$rowClass = "itsm-vhost-expi";
			
			$results['refs'][] = [$vhost->getId()];
			$results['rowClasses'][] = [$rowClass];
			$results['data'][] = [$vhost->getSubDomain(), $vhost->getDomain(), $registrar->getCode(), $vhost->getName(), $status->getName(), ($host->getSystemName() . " (" . $host->getIpAddress() . ")")];
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
			<td>Subdomain</td>
			<td><input type="text" name="subdomain" value="<?=ifSet($_GET['subdomain'])?>"></td>
			<td>Domain</td>
			<td><input type="text" name="domain" value="<?=ifSet($_GET['domain'])?>"></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" name="name" value="<?=ifSet($_GET['name'])?>"></td>
			<td>Status</td>
			<td>
				<select name="status[]" multiple size=3>
				<?php
					foreach(getAttributes('itsm', 'wdns') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['status']) ? " selected" : "")?>><?=$attribute->getName()?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>Host Asset</td>
				<td><input type="text" name="hostAsset" value="<?=ifSet($_GET['hostAsset'])?>"></td>
				<td>Registrar</td>
				<td><input type="text" name="registrar" value="<?=ifSet($_GET['registrar'])?>"></td>
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