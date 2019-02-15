<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['commodityType']) OR !is_array($_GET['commodityType']))
		$_GET['commodityType'] = [];
	
	if(!isset($_GET['assetType']) OR !is_array($_GET['assetType']))
		$_GET['assetType'] = [];
	
	if(isset($_GET['submitted']))
	{
		
		$commodities = itsmcore\getCommodities(wildcard(ifSet($_GET['code'])), wildcard(ifSet($_GET['name'])),
			wildcard(ifSet($_GET['manufacturer'])), wildcard(ifSet($_GET['model'])), "%", $_GET['commodityType'], $_GET['assetType']);
			
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?c=";
		$results['head'] = ['Code', 'Name', 'Type', 'Asset Type', 'Manufacturer', 'Model'];
		$results['align'] = ["right", "left", "left", "left", "left", "left"];
		$results['refs'] = [];
		$results['data'] = [];
		$results['widths'] = ["100px", "", "", "", "", ""];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($commodities as $commodity)
		{
			$commodityType = new Attribute($commodity->getCommodityType());
			$commodityType->load();
			
			$assetType = new Attribute($commodity->getAssetType());
			$assetType->load();
			
			$results['refs'][] = [$commodity->getId()];
			$results['data'][] = [$commodity->getCode(), $commodity->getName(), $commodityType->getName(), $assetType->getName(), $commodity->getManufacturer(), $commodity->getModel()];
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
			<td>Type</td>
			<td>
				<select name="commodityType[]" multiple size=3>
				<?php
					foreach(getAttributes('itsm', 'coty') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['commodityType']) ? " selected" : "")?>><?=$attribute->getName()?></option>
						<?php
					}
				?>
				</select>
			</td>
			<td>Asset Type</td>
			<td>
			<select name="assetType[]" multiple size=3>
			<?php
				foreach(getAttributes('itsm', 'asty') as $attribute)
				{
					?>
					<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['assetType']) ? " selected" : "")?>><?=$attribute->getName()?></option>
					<?php
				}
			?>
			</select>
			</td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>Manufacturer</td>
				<td><input type="text" name="manufacturer" value="<?=ifSet($_GET['manufacturer'])?>"></td>
				<td>Model</td>
				<td><input type="text" name="model" value="<?=ifSet($_GET['model'])?>"></td>
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