<form class="table-form form" method="post" id="purchaseorder-form">
	<h2 class="region-title">Order Details</h2>
	<table class="table-display purchaseorder-display">
		<tr>
			<td class="required">Vendor Code</td>
			<td>
				<input id="vendorCode" type="text" name="vendorCode" value="<?=htmlentities(ifSet($_POST['vendorCode']))?>">
			</td>
			<td>Vendor Name</td>
			<td><input id="vendorName" type="text" readonly name="vendorName" value="<?=htmlentities(ifSet($_POST['vendorName']))?>"</td>
		</tr>
		<tr>
			<td class="required">Ship to Warehouse</td>
			<td>
				<input id="warehouseCode" type="text" name="warehouseCode" value="<?=htmlentities(ifSet($_POST['warehouseCode']))?>">
			</td>
			<td>Warehouse Name</td>
			<td><input id="warehouseName" type="text" readonly name="warehouseName" value="<?=htmlentities(ifSet($_POST['warehouseName']))?>"></td>
		</tr>
		<tr>
			<td class="required">Order Date</td>
			<td><input type="text" class="date-input" name="orderDate" value="<?=htmlentities(ifSet($_POST['orderDate']))?>"></td>
		</tr>
	</table>
	<h2 class="region-title">Additional Details</h2>
	<table class="table-display purchaseorder-display">
		<tr>
			<td>Notes</td>
			<td><textarea name="notes"><?=htmlentities(ifSet($_POST['notes']))?></textarea></td>
		</tr>
	</table>
</form>
<script>
	// Update form fields based on selected value
	function ITSMCore_Inventory_PurchaseOrders_updateVendorDetails()
	{
		$.ajax({
			url: '<?=SITE_URI?>inventory/purchaseorders/api?vendor=' + $('#vendorCode').val(),
			type:'GET',
			success: function(data)
			{
				var vendorData = JSON.parse($(data).find('#encoded-data').html());
				
				$('#vendorName').val(vendorData.name);
			}
		});
	}
	
	function ITSMCore_Inventory_PurchaseOrders_updateWarehouseDetails()
	{
		$.ajax({
			url: '<?=SITE_URI?>inventory/purchaseorders/api?warehouse=' + $('#warehouseCode').val(),
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
		$("#vendorCode").change(function(){ITSMCore_Inventory_PurchaseOrders_updateVendorDetails()});
		$("#warehouseCode").change(function(){ITSMCore_Inventory_PurchaseOrders_updateWarehouseDetails()});
		
		// Generate autocomplete list
		$.ajax({
			url: '<?=SITE_URI?>inventory/purchaseorders/api?vendorCodes',
			type: 'GET',
			success: function(data)
			{
				var vendorCodes = JSON.parse($(data).find('#encoded-data').html());
				$("#vendorCode").autocomplete({
					source: vendorCodes,
					select: function(e, ui)
					{
						$('#vendorCode').val(ui.item.value);
						ITSMCore_Inventory_PurchaseOrders_updateVendorDetails()
					},
					change: function(e, ui)
					{
						ITSMCore_Inventory_PurchaseOrders_updateVendorDetails()
					}
				});
			}
		});
		
		$.ajax({
			url: '<?=SITE_URI?>inventory/purchaseorders/api?warehouseCodes',
			type: 'GET',
			success: function(data)
			{
				var warehouseCodes = JSON.parse($(data).find('#encoded-data').html());
				$("#warehouseCode").autocomplete({
					source: warehouseCodes,
					select: function(e, ui)
					{
						$('#warehouseCode').val(ui.item.value);
						ITSMCore_Inventory_PurchaseOrders_updateWarehouseDetails()
					},
					change: function(e, ui)
					{
						ITSMCore_Inventory_PurchaseOrders_updateWarehouseDetails()
					}
				});
			}
		});
	});
</script>