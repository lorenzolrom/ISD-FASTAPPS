<?php
	use itsmcore as itsmcore;
	
	$worksheet = new itsmcore\AssetWorksheet();
	
	if(isset($_GET['function']))
	{
		if($_GET['function'] == "view") // View worksheet
		{
			$results['type'] = "table";
			$results['linkColumn'] = 1;
			$results['href'] = SITE_URI . "inventory/assets/view?a=";
			$results['head'] = ['', 'Asset Tag', 'Code', 'Name', 'Asset Type', 'Serial Number', 'Location', 'Warehouse', 'Verified'];
			$results['refs'] = [];
			$results['data'] = [];
			$results['rowClass'] = [];
			$results['widths'] = ["10px", "", "", "", "", "", "", "", "10px"];
			$results['selectColumn'] = 0;
			
			foreach($worksheet->getAssets() as $asset)
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
				
				// Determine row style
				$rowClass = "itsm-asset-normal";
				
				if($asset->getDiscarded() == 1)
					$rowClass = "itsm-asset-discarded";
				if($asset->getWarehouse() !== NULL)
					$rowClass = "itsm-asset-warehouse";
				
				$results['rowClasses'][] = [$rowClass];
				$results['refs'][] = [$asset->getId()];
				$results['data'][] = [$asset->getId(), $asset->getAssetTag(), $commodity->getCode(), $commodity->getName(), $assetType->getName(), ($asset->getSerialNumber() === null ? "" : $asset->getSerialNumber()), $building->getCode() . " " . $location->getCode(), ($warehouse->getCode() === null ? "" : $warehouse->getCode()), ($asset->getVerified() == 1 ? "✔" : "")];
			}
			?>
				<form class="search-form table-form" id="action-form">
					<table class="table-display">
						<tr>
							<td>Action</td>
							<td>
								<select name="function">
									<option value="removeall">Clear</option>
									<option value="verify">Verify</option>
									<option value="unverify">Un-Verify</option>
									<option value="return">Return To Warehouse</option>
									<option value="assign">Assign Location</option>
									<option value="discard">Discard</option>
								</select>
							</td>
							<td><input class="button confirm-button" type="submit" value="Go"></td>
							<td><span class="button-noveil" id="removeSelected">Remove Selected</span></td>
						</tr>
					</table>
				</form>
				<div id="results">
				</div>
				<script>
					$('#removeSelected').click(function(){
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
							
							window.location = "<?=SITE_URI?>inventory/assets/worksheet?function=remove" + assetString;
						}
					});
					// Show results
					showResults('results', <?=json_encode($results)?>, <?=RESULTS_PER_PAGE?>)
				</script>
			<?php
		}
		else if($_GET['function'] == "add") // Add asset/assets
		{
			if(isset($_GET['a']))
			{
				// Check if parameter is an array
				if(is_array($_GET['a']))
				{
					$success = 0;
					$failure = 0;
					
					// Process multiple adds
					foreach($_GET['a'] as $assetId)
					{
						$asset = new itsmcore\Asset($assetId);
						
						if($asset->load())
						{
							if($asset->getDiscarded() == 0 AND $worksheet->addAsset($asset->getId()))
								$success++;
							else
								$failure++;
						}
					}
					
					header("Location: " . SITE_URI . "inventory/assets?NOTICE=" . $success . " Assets Added To Worksheet" . ($failure != 0 ? (", " . $failure . " Could Not Be Added") : ""));
					exit();
				}
				else
				{
					$asset = new itsmcore\Asset($_GET['a']);
					
					if($asset->load())
					{
						if($worksheet->addAsset($asset->getId()))
						{
							header("Location: " . SITE_URI . "inventory/assets/view?a=" . $_GET['a'] . "&NOTICE=Added To Worksheet");
							exit();
						}
						else
						{
							header("Location: " . SITE_URI . "inventory/assets/view?a=" . $_GET['a'] . "&NOTICE=Already In Worksheet");
							exit();
						}
					}
					else
						throw new AppException("Asset Is Invalid", "P04");
				}
			}
			else
				throw new AppException("Asset Not Defined", "P03");
		}
		else if($_GET['function'] == "remove") // Remove asset/assets
		{
			if(isset($_GET['a']))
			{
				// Check if parameter is an array
				if(is_array($_GET['a']))
				{
					// Process multiple removes
					$success = 0;
					$failure = 0;
					
					// Process multiple adds
					foreach($_GET['a'] as $assetId)
					{
						$asset = new itsmcore\Asset($assetId);
					
						if($worksheet->removeAsset($asset->getId()))
							$success++;
						else
							$failure++;
					}
					
					header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=" . $success . " Assets Removed From Worksheet" . ($failure != 0 ? (", " . $failure . " Could Not Be Removed") : ""));
					exit();
				}
				else
				{
					// Process single remove
					$asset = new itsmcore\Asset($_GET['a']);
					
					if($asset->load())
					{
						if($worksheet->removeAsset($asset->getId()))
						{
							header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Removed From Worksheet");
							exit();
						}
						else
						{
							header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Not In Worksheet");
							exit();
						}
					}
					else
						throw new AppException("Asset Not Defined", "P03");
				}
			}
			else
				throw new AppException("Asset Not Defined", "P03");
		}
		else if($_GET['function'] == "removeall") // Remove all assets from worksheet
		{
			$worksheet->removeAll();
			header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Worksheet Cleared");
			exit();
		}
		else if($_GET['function'] == "return") // Prompt user for warehouse and return all assets
		{
			if(!empty($_POST))
			{
				
				// Validation
				
				$warehouse = new itsmcore\Warehouse();
				
				if(!ifSet($_POST['warehouseCode']) OR !$warehouse->loadFromCode($_POST['warehouseCode']))
					$faSystemErrors[] = "Warehouse Code Is Invalid";
				
				if(empty($faSystemErrors))
				{
					if($worksheet->returnToWarehouse($warehouse->getId()))
					{
						header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Returned Assets To Warehouse");
						exit();
					}
					else
					{
						header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Could Not Return One Or More Assets");
						exit();
					}
				}
			}
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="return">Return</span>
				<a class="button" href="<?=SITE_URI?>inventory/assets/worksheet?function=view">Cancel</a>
			</div>
			<form class="table-form form" id="return-form" method="post">
				<h2 class="region-title">Warehouse Profile</h2>
				<table class="table-display location-display">
					<tr>
						<td>Warehouse Code</td>
						<td>
							<input id="warehouseCode" type="text" name="warehouseCode" value="<?=ifSet($_POST['warehouseCode'])?>">
						</td>
						<td>Warehouse Name</td>
						<td>
							<input readonly id="warehouseName" type="text" name="warehouseName" value="<?=ifSet($_POST['warehouseName'])?>">
						</td>
					</tr>
				</table>
			</form>
			<script>
				// Update form fields based on selected value
				function ITSMCore_Inventory_Assets_updateWarehouseDetails()
				{
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?warehouse=' + $('#warehouseCode').val(),
						type:'GET',
						success: function(data)
						{
							var warehouseData = JSON.parse($(data).find('#encoded-data').html());
							
							$('#warehouseName').val(warehouseData.name);
						}
					});
				}
				
				$(document).ready(function(){
					// Add listener for code change
					$("#warehouseCode").change(function(){ITSMCore_Inventory_Assets_updateWarehouseDetails()});
					
					// Generate autocomplete list
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?warehouseCodes',
						type: 'GET',
						success: function(data)
						{
							var warehouseCodes = JSON.parse($(data).find('#encoded-data').html());
							$("#warehouseCode").autocomplete({
								source: warehouseCodes,
								select: function(e, ui)
								{
									$('#warehouseCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateWarehouseDetails();
								},
								change: function(e, ui)
								{
									$('#warehouseCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateWarehouseDetails();
								}
							});
						}
					});
				});
			</script>
			<?php
		}
		else if($_GET['function'] == "discard") // Discard all assets
		{
			if($worksheet->discard())
			{
				header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Assets Discarded");
				exit();
			}
			else
			{
				header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Could Not Discard One Or More Assets");
				exit();
			}
		}
		else if($_GET['function'] == "assign") // Prompt user for location and return all assets
		{
			if(!empty($_POST))
			{
				// Validation
				$location = new facilitiescore\Location();
				
				if($location->loadFromCode($_POST['buildingCode'], $_POST['locationCode']))
				{
					// Assign asset
					if($worksheet->assignToLocation($location->getId()))
					{
						header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Assigned Assets To Location");
						exit();
					}
					else
					{
						header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Could Not Assign One Or More Assets");
						exit();
					}
				}
				else
					$faSystemErrors[] = "Location Is Invalid";
			}
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="location">Assign</span>
				<a class="button" href="<?=SITE_URI?>inventory/assets/worksheet?function=view">Cancel</a>
			</div>
			<form class="table-form form" id="location-form" method="post">
				<h2 class="region-title">Location Profile</h2>
				<table class="table-display location-display">
					<tr>
						<td>Building Code</td>
						<td>
							<input id="buildingCode" type="text" name="buildingCode" value="<?=ifSet($_POST['buildingCode'])?>">
						</td>
						<td>Building Name</td>
						<td>
							<input readonly id="buildingName" type="text" name="buildingName" value="<?=ifSet($_POST['buildingName'])?>">
						</td>
					</tr>
					<tr>
						<td>Location Code</td>
						<td>
							<input id="locationCode" type="text" name="locationCode" value="<?=ifSet($_POST['locationCode'])?>">
						</td>
						<td>Location Name</td>
						<td>
							<input readonly id="locationName" type="text" name="locationName" value="<?=ifSet($_POST['locationName'])?>">
						</td>
					</tr>
				</table>
			</form>
			<script>
				// Update form fields based on selected value
				function ITSMCore_Inventory_Assets_updateBuildingDetails()
				{
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?building=' + $('#buildingCode').val(),
						type:'GET',
						success: function(data)
						{
							var buildingData = JSON.parse($(data).find('#encoded-data').html());
							
							$('#buildingName').val(buildingData.name);
						}
					});
				}
				
				function ITSMCore_Inventory_Assets_updateLocationDetails()
				{
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?location=' + $('#locationCode').val() + "&buildingCode=" + $('#buildingCode').val(),
						type:'GET',
						success: function(data)
						{
							var locationData = JSON.parse($(data).find('#encoded-data').html());
							
							$('#locationName').val(locationData.name);
						}
					});
				}
				
				function ITSMCore_Inventory_Assets_updateLocationCodeList()
				{
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?locationCodes=' + $('#buildingCode').val(),
						type: 'GET',
						success: function(data)
						{
							var locationCodes = JSON.parse($(data).find('#encoded-data').html());
							$("#locationCode").autocomplete({
								source: locationCodes,
								select: function(e, ui)
								{
									$('#locationCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateLocationDetails();
								},
								change: function(e, ui)
								{
									$('#locationCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateLocationDetails();
								}
							});
						}
					});
				}
				
				$(document).ready(function(){
					// Add listener for code change
					$("#buildingCode").change(function(){ITSMCore_Inventory_Assets_updateBuildingDetails()});
					
					// Generate autocomplete list
					$.ajax({
						url: '<?=SITE_URI?>inventory/assets/api?buildingCodes',
						type: 'GET',
						success: function(data)
						{
							var buildingCodes = JSON.parse($(data).find('#encoded-data').html());
							$("#buildingCode").autocomplete({
								source: buildingCodes,
								select: function(e, ui)
								{
									$('#buildingCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateBuildingDetails();
									ITSMCore_Inventory_Assets_updateLocationCodeList();
								},
								change: function(e, ui)
								{
									$('#buildingCode').val(ui.item.value);
									ITSMCore_Inventory_Assets_updateBuildingDetails();
									ITSMCore_Inventory_Assets_updateLocationCodeList();
								}
							});
						}
					});
				});
			</script>
			<?php
		}
		else if($_GET['function'] == "verify") // Verify all assets
		{
			$worksheet->verify();
			header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Assets Verified");
			exit();
		}
		else if($_GET['function'] == "unverify") // Un-Verify all assets
		{
			$worksheet->unverify();
			header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view&NOTICE=Assets Un-Verified");
			exit();
		}
		else
			throw new AppException("Function Is Invalid", "P04");
	}
	else
	{
		header("Location: " . SITE_URI . "inventory/assets/worksheet?function=view");
		exit();
	}
?>