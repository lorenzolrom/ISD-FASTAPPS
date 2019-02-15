<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['po']))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		if($purchaseOrder->loadFromNumber($_GET['po']))
		{
			// Check if already received
			if($purchaseOrder->getReceived() == 1)
				throw new AppException("Purchase Order Has Been Received", "P04");
			
			if($purchaseOrder->getCanceled() == 1)
				throw new AppException("Purchase Order Has Been Canceled", "P05");
			
			if($purchaseOrder->getSent() == 1)
				throw new AppException("Purchase Order Has Been Sent", "P04");
			
			if($_GET['function'] == "add")
			{
				if(!empty($_POST))
				{
					/////
					// Validation
					/////
					
					// Commodity Code
					$commodity = null;
					
					if(!isset($_POST['commodityCode']))
						$faSystemErrors[] = "Commodity Code Required";
					else
					{
						$commodity = new itsmcore\Commodity();
						
						if(!$commodity->loadFromCode($_POST['commodityCode']))
							$faSystemErrors[] = "Commodity Is Invalid";
					}
					
					// Quantity
					if(!ctype_digit($_POST['quantity']))
						$faSystemErrors[] = "Quantity Is Invalid";
					
					// Unit Cost
					if(strlen($_POST['unitCost']) == 0)
						$faSystemErrors[] = "Unit Cost Required";
					else if(!is_numeric($_POST['unitCost']))
						$faSystemErrors[] = "Unit Cost Must Be Numeric";
					else if($_POST['unitCost'] < 0)
						$faSystemErrors[] = "Unit Cost Must Be Positive";
					
					if(empty($faSystemErrors))
					{
						if($purchaseOrder->addCommodity($commodity->getId(), $_POST['quantity'], $_POST['unitCost']))
						{
							?>
							<script>
								window.opener.location.reload();
								window.close();
							</script>
							<?php
						}
						else
							$faSystemErrors[] = "Failed To Add Commodity";
					}
				}
				
				?>
				<h2 class="region-title">Add Commodity</h2>
				<form class="basic-form form" method="post">
					<p>
						<span class="required">Commodity Code</span>
						<input id="commodityCode" type="text" name="commodityCode" value="<?=ifSet($_POST['commodityCode'])?>">
					</p>
					<p>
						<span>Commodity Name</span>
						<input id="commodityName" type="text" readonly name="commodityName" value="<?=ifSet($_POST['commodityName'])?>">
					</p>
					<p>
						<span class="required">Quantity</span>
						<input type="number" name="quantity" min="1" value="<?=ifSet($_POST['quantity'])?>">
					</p>
					<p>
						<span class="required">Unit Cost</span>
						<input id="unitCost" type="text" name="unitCost" value="<?=ifSet($_POST['unitCost'])?>">
					</p>
					<input type="submit" class="button" value="Add" accesskey="a">
					<input type="button" class="button window-close-button" value="Cancel" accesskey="c">
				</form>
				<script>
					// Update form fields based on selected value
					function ITSMCore_Inventory_Commodoties_updateCommodityDetails()
					{
						$.ajax({
							url: '<?=SITE_URI?>inventory/purchaseorders/api?commodity=' + $('#commodityCode').val(),
							type:'GET',
							success: function(data)
							{
								var commodityData = JSON.parse($(data).find('#encoded-data').html());
								
								$('#commodityName').val(commodityData.name);
								$('#unitCost').val(commodityData.unitCost);
							}
						});
					}
					
					$(document).ready(function(){
						// Add listener for code change
						$("#commodityCode").change(function(){ITSMCore_Inventory_Commodoties_updateCommodityDetails()});
						
						// Generate autocomplete list
						$.ajax({
							url: '<?=SITE_URI?>inventory/purchaseorders/api?commodityCodes',
							type: 'GET',
							success: function(data)
							{
								var commodityCodes = JSON.parse($(data).find('#encoded-data').html());
								$("#commodityCode").autocomplete({
									source: commodityCodes,
									select: function(e, ui)
									{
										$('#commodityCode').val(ui.item.value);
										ITSMCore_Inventory_Commodoties_updateCommodityDetails()
									},
									change: function(e, ui)
									{
										ITSMCore_Inventory_Commodoties_updateCommodityDetails()
									}
								});
							}
						});
					});
				</script>
				<?php
			}
			else if($_GET['function'] == 'remove' AND isset($_GET['id']))
			{
				$purchaseOrder->removeCommodity($_GET['id']);
				?>
				<script>
					window.opener.location.reload();
					window.close();
				</script>
				<?php
			}
			else
				throw new AppException("Function Is Invalid", "P04");
		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
?>