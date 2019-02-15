<?php
	use itsmcore as itsmcore;
	
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
					
					$warehouse = new itsmcore\Warehouse();
					
					if((ifSet($_POST['warehouseCode']) === FALSE) OR !$warehouse->loadFromCode($_POST['warehouseCode']))
						$faSystemErrors[] = "Warehouse Code Is Invalid";
					
					if(empty($faSystemErrors))
					{
						if($_GET['function'] == "return")
						{
							if($asset->getWarehouse() !== NULL)
								throw new AppException("Asset Already In Warehouse", "B01");
			
							if($asset->returnToWarehouse($warehouse->getId()))
							{
								header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Returned To Warehouse");
								exit();
							}
							else
								$faSystemErrors[] = "Failed To Return Asset To Warehouse";
						}
						else if($_GET['function'] == "move")
						{
							if($asset->returnToWarehouse($warehouse->getId()))
							{
								header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Warehouse Changed");
								exit();
							}
							else
								$faSystemErrors[] = "Failed To Move Asset To New Warehouse";
						}
						else
							throw new AppException("Function Is Invalid", "P04");
					}
				}			
			
			?>
			<div class="button-bar">
				<?php
					if($_GET['function'] == "return")
					{
						?><span class="button form-submit-button" id="return">Return</span><?php
					}
					else if($_GET['function'] == "move")
					{
						?><span class="button form-submit-button" id="return">Move</span><?php
					}
				?>
				<a class="button" href="<?=SITE_URI?>inventory/assets/view?a=<?=$asset->getId()?>">Cancel</a>
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
									ITSMCore_Inventory_Assets_updateWarehouseDetails();
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