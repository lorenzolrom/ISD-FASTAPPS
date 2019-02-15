<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['submitted']))
	{
		$hosts = itsmcore\getHosts(wildcard($_GET['assetTag']), wildcard($_GET['ipAddress']), wildcard($_GET['macAddress']), 
			wildcard($_GET['systemName']), wildcard($_GET['systemCPU']), wildcard($_GET['systemRAM']), wildcard($_GET['systemOS']), wildcard($_GET['systemDomain']));
			
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = SITE_URI . "devices/hosts/view?h=";
		$results['head'] = ['IP Address', 'MAC Address', 'Asset', 'System Name'];
		$results['refs'] = [];
		$results['data'] = [];
		$results['align'] = ["center", "left", "left", "left"];
		$results['widths'] = ["150px", "150px", "100px", ""];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($hosts as $host)
		{
			$asset = new itsmcore\Asset($host->getAsset());
			$asset->load();
			
			$results['refs'][] = [$host->getId()];
			$results['data'][] = [$host->getIpAddress(), $host->getMacAddress(), $asset->getAssetTag(), $host->getSystemName()];
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
			<td>IP Address</td>
			<td><input type="text" name="ipAddress" value="<?=ifSet($_GET['ipAddress'])?>"></td>
			<td>MAC Address</td>
			<td><input type="text" name="macAddress" value="<?=ifSet($_GET['macAddress'])?>"></td>
		</tr>
		<tr>
			<td>Asset Tag</td>
			<td><input type="text" name="assetTag" value="<?=ifSet($_GET['assetTag'])?>"></td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>System Name</td>
				<td><input type="text" name="systemName" value="<?=ifSet($_GET['systemName'])?>"></td>
				<td>System CPU</td>
				<td><input type="text" name="systemCPU" value="<?=ifSet($_GET['systemCPU'])?>"></td>
			</tr>
			<tr>
				<td>System RAM</td>
				<td><input type="text" name="systemRAM" value="<?=ifSet($_GET['systemRAM'])?>"></td>
				<td>System OS</td>
				<td><input type="text" name="systemOS" value="<?=ifSet($_GET['systemOS'])?>"></td>
			</tr>
			<tr>
				<td>System Domain</td>
				<td><input type="text" name="systemDomain" value="<?=ifSet($_GET['systemDomain'])?>"></td>
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