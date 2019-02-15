<?php
	use itsmcore as itsmcore;
	use facilitiescore as facilitiescore;
	
	$worksheet = new itsmcore\AssetWorksheet();
	
	if(!isset($_GET['assetType']) OR !is_array($_GET['assetType']))
		$_GET['assetType'] = [];
	
	if(!isset($_GET['commodityType']) OR !is_array($_GET['commodityType']))
		$_GET['commodityType'] = [];
	
	$inWarehouse = [];
	$discarded = [];
	$verified = [];
	
	if(isset($_GET['warehouseYes']))
		$inWarehouse[] = 1;
	if(isset($_GET['warehouseNo']))
		$inWarehouse[] = 0;
	if(isset($_GET['discardedYes']))
		$discarded[] = 1;
	if(isset($_GET['discardedNo']))
		$discarded[] = 0;
	if(isset($_GET['verifiedYes']))
		$verified[] = 1;
	if(isset($_GET['verifiedNo']))
		$verified[] = 0;
	
	if(isset($_GET['submitted']))
	{
		$assets = itsmcore\getAssets(wildcard(ifSet($_GET['assetTag'])), wildcard(ifSet($_GET['serialNumber'])), $inWarehouse, $discarded, $verified, 
			wildcard(ifSet($_GET['buildingCode'])), wildcard(ifSet($_GET['locationCode'])), wildcard(ifSet($_GET['warehouseCode'])), 
			wildcard(ifSet($_GET['manufacturer'])), wildcard(ifSet($_GET['model'])), wildcard(ifSet($_GET['purchaseOrderNumber'])), 
			wildcard(ifSet($_GET['commodityCode'])), wildcard(ifSet($_GET['commodityName'])), $_GET['commodityType'], $_GET['assetType']);
			
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 2;
		$results['href'] = getURI() . "/view?a=";
		$results['head'] = ['In W.S.', '', 'Asset Tag', 'Code', 'Name', 'Asset Type', 'Serial Number', 'Location', 'Warehouse', 'Verified', 'R.O. #'];
		$results['align'] = ["center", "center", "right", "left", "left", "left", "left", "left", "left", "center", "right"];
		$results['refs'] = [];
		$results['data'] = [];
		$results['rowClasses'] = [];
		$results['widths'] = ["45px", "10px", "100px", "50px", "", "", "", "100px", "", "10px", "50px"];
		$results['selectColumn'] = 1;
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($assets as $asset)
		{
			$commodity = new itsmcore\Commodity($asset->getCommodity());
			$commodity->load();
			
			$location = new facilitiescore\Location($asset->getLocation());
			$location->load();
			
			$building = new facilitiesCore\Building($location->getBuilding());
			$building->load();
			
			$warehouse = new itsmcore\Warehouse($asset->getWarehouse());
			$warehouse->load();
			
			$assetType = new Attribute($commodity->getAssetType());
			$assetType->load();
			
			$rowClass = "itsm-asset-warehouse";
			
			// Determine row style
			if($asset->getLocation() !== NULL)
				$rowClass = "itsm-asset-normal";
			if($asset->getDiscarded() == 1)
				$rowClass = "itsm-asset-discarded";
			
			$results['rowClasses'][] = [$rowClass];
			$results['refs'][] = [$asset->getId()];
			$results['data'][] = [($asset->isInWorksheet() ? "✔" : ""), $asset->getId(), $asset->getAssetTag(), $commodity->getCode(), $commodity->getName(), $assetType->getName(), ($asset->getSerialNumber() === null ? "" : $asset->getSerialNumber()), $building->getCode() . " " . $location->getCode(), ($warehouse->getCode() === null ? "" : $warehouse->getCode()), ($asset->getVerified() == 1 ? "✔" : ""), ($asset->getReturnOrder() !== FALSE ? $asset->getReturnOrder() : "")];
		}
	}
	else
	{
		$_GET['warehouseYes'] = "true";
		$_GET['warehouseNo'] = "true";
		$_GET['discardedYes'] = "true";
		$_GET['discardedNo'] = "true";
		$_GET['verifiedYes'] = "true";
		$_GET['verifiedNo'] = "true";
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="search" accesskey="s">Search</span>
	<a class="button" href="<?=SITE_URI?>inventory/assets/worksheet?function=view" accesskey="w">View Worksheet</a>
	<span class="button-noveil" id="addToWorksheet">Add To Worksheet (<?=$worksheet->getCount()?>)</span>
</div>
<form class="search-form table-form large-search-form form" id="search-form">
	<input type="hidden" name="submitted" value="true">
	<table class="table-display">
		<tr>
			<td>Asset Tag</td>
			<td><input type="text" name="assetTag" value="<?=ifSet($_GET['assetTag'])?>"></td>
			<td>Serial Number</td>
			<td><input type="text" name="serialNumber" value="<?=ifSet($_GET['serialNumber'])?>"></td>
		</tr>
		<tr>
			<td>Warehouse</td>
			<td><input type="checkbox" value="true" name="warehouseYes"<?=(isset($_GET['warehouseYes']) ? " checked" : "")?>><label>Yes</label> <input type="checkbox" value="true" name="warehouseNo"<?=(isset($_GET['warehouseNo']) ? " checked" : "")?>><label>No</label></td>
			<td>Discarded</td>
			<td><input type="checkbox" value="true" name="discardedYes"<?=(isset($_GET['discardedYes']) ? " checked" : "")?>><label>Yes</label> <input type="checkbox" value="true" name="discardedNo"<?=(isset($_GET['discardedNo']) ? " checked" : "")?>><label>No</label></td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>Building Code</td>
				<td><input type="text" name="buildingCode" value="<?=ifSet($_GET['buildingCode'])?>"></td>
				<td>Location Code</td>
				<td><input type="text" name="locationCode" value="<?=ifSet($_GET['locationCode'])?>"></td>
			</tr>
			<tr>
				<td>Warehouse Code</td>
				<td><input type="text" name="warehouseCode" value="<?=ifSet($_GET['warehouseCode'])?>"></td>
				<td>P.O. #</td>
				<td><input type="text" name="purchaseOrderNumber" value="<?=ifSet($_GET['purchaseOrderNumber'])?>"></td>
			</tr>
			<tr>
				<td>Manufacturer</td>
				<td><input type="text" name="manufacturer" value="<?=ifSet($_GET['manufacturer'])?>"></td>
				<td>Model</td>
				<td><input type="text" name="model" value="<?=ifSet($_GET['model'])?>"></td>
			</tr>
			<tr>
				<td>Commodity Code</td>
				<td><input type="text" name="commodityCode" value="<?=ifSet($_GET['commodityCode'])?>"></td>
				<td>Commodity Name</td>
				<td><input type="text" name="commodityName" value="<?=ifSet($_GET['commodityName'])?>"></td>
			</tr>
			<tr>
				<td>Commodity Type</td>
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
			<tr>
				<td>Verified</td>
				<td><input type="checkbox" value="true" name="verifiedYes"<?=(isset($_GET['verifiedYes']) ? " checked" : "")?>><label>Yes</label> <input type="checkbox" value="true" name="verifiedNo"<?=(isset($_GET['verifiedNo']) ? " checked" : "")?>><label>No</label></td>
				<td>Results Per Page</td>
				<td><input maxlength=3 class="results-per-page tiny-input" type="text" name="resultsPerPage" value="<?=ifSet($_GET['resultsPerPage'])?>"></td>
			</tr>
		</tbody>
	</table>
	<div class="button-bar">
		<span class="button-noveil search-additional-field-toggle">Show More</span>
	</div>
</form>
<script>
	// Bulk add to worksheet
	$('#addToWorksheet').click(function(){
		var assets = new Array();
		
		$.each($('.table-row-select:checkbox:checked'), function(i, item){
			assets.push(item.value);
		});
		
		if(assets.length == 0)
		{
			alert('No Assets Selected');
		}
		else
		{		
			var assetString = "";
			
			$.each(assets, function(i, item)
			{
				assetString += "&a%5B%5D=" + item;
			});
			
			window.location = "<?=SITE_URI?>inventory/assets/worksheet?function=add" + assetString;
		}
	});
</script>
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