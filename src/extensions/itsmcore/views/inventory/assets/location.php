<?php
	use itsmcore as itsmcore;
	use facilitiescore as facilitiescore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['a']))
	{
		$asset = new itsmcore\Asset($_GET['a']);
		
		if($asset->load())
		{
			if($asset->getDiscarded() == 1)
				throw new AppException("Asset Is Discarded", "P04");
			
			if($asset->getReturnOrder() !== FALSE)
				throw new AppException("Asset Is On Return Order", "P04");
			
			if(!empty($_POST))
			{
				// Validation
				$location = new facilitiescore\Location();
				
				if(ifSet($_POST['buildingCode']) === FALSE)
					$faSystemErrors[] = "Building Code Required";
				
				if(ifSet($_POST['locationCode']) === FALSE)
					$faSystemErrors[] = "Location Code Required";
				
				if(empty($faSystemErrors))
				{
					if($location->loadFromCode($_POST['buildingCode'], $_POST['locationCode']))
					{
						// Assign asset
						if($asset->assignLocation($location->getId()))
						{							
							if($_GET['function'] == "assign")
							{
								header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Assigned To Location");
								exit();
							}
							else if($_GET['function'] == "move")
							{
								header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Location Changed");
								exit();
							}
							else
								throw new AppException("Function Is Invalid", "P04");
						}
						else
							$faSystemErrors[] = "Failed To Assign Location";
					}
				}
				else
					$faSystemErrors[] = "Location Is Invalid";
			}
			
			?>
			<div class="button-bar">
				<?php
					if($_GET['function'] == "assign")
					{
						?><span class="button form-submit-button" id="location">Assign</span><?php
					}
					else if($_GET['function'] == "move")
					{
						?><span class="button form-submit-button" id="location">Move</span><?php
					}
				?>
				<a class="button" href="<?=SITE_URI?>inventory/assets/view?a=<?=$asset->getId()?>">Cancel</a>
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
		else
			throw new AppException("Asset Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Not Defined", "P03");
?>