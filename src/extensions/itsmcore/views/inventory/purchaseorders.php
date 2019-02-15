<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['status']) OR !is_array($_GET['status']))
		$_GET['status'] = [];
	
	if(isset($_GET['submitted']))
	{
		$purchaseOrders = itsmcore\getPurchaseOrders(wildcard(ifSet($_GET['number'])), wildcard(ifSet($_GET['vendorCode'])),
			ifSet($_GET['orderDateStart']), ifSet($_GET['orderDateEnd']), wildcard(ifSet($_GET['warehouseCode'])), $_GET['status']);
		
		// Build Results Array
		$results['type'] = "table";
		$results['linkColumn'] = 0;
		$results['href'] = getURI() . "/view?po=";
		$results['head'] = ['Number', 'Order Date', 'Status', 'Vendor', 'Warehouse'];
		$results['align'] = ["center", "left", "left", "left", "left"];
		$results['widths'] = ["60px", "150px", "150px", "", "150px"];
		$results['refs'] = [];
		$results['data'] = [];
		
		$resultsPerPage = RESULTS_PER_PAGE;
		
		if(isset($_GET['resultsPerPage']) AND ctype_digit($_GET['resultsPerPage']) AND $_GET['resultsPerPage'] < 1000 AND $_GET['resultsPerPage'] > 0)
			$resultsPerPage = $_GET['resultsPerPage'];
		
		foreach($purchaseOrders as $purchaseOrder)
		{
			$status = new Attribute($purchaseOrder->getStatus());
			$status->load();
			
			$vendor = new itsmcore\Vendor($purchaseOrder->getVendor());
			$vendor->load();
			
			$warehouse = new itsmcore\Warehouse($purchaseOrder->getWarehouse());
			$warehouse->load();
			
			$results['refs'][] = [$purchaseOrder->getNumber()];
			$results['data'][] = [$purchaseOrder->getNumber(), $purchaseOrder->getOrderDate(), $status->getName(), $vendor->getName(), $warehouse->getCode()];
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
			<td>Number</td>
			<td><input type="text" name="number" value="<?=ifSet($_GET['number'])?>"></td>
		</tr>
		<tr>
			<td>Order Date Start</td>
			<td><input class="date-input" type="text" name="orderDateStart" value="<?=ifSet($_GET['orderDateStart'])?>"></td>
			<td>Order Date End</td>
			<td><input class="date-input" type="text" name="orderDateEnd" value="<?=ifSet($_GET['orderDateEnd'])?>"></td>
		</tr>
		<tbody class="additional-fields">
			<tr>
				<td>Vendor Code</td>
				<td><input type="text" name="vendorCode" value="<?=ifSet($_GET['vendorCode'])?>"></td>
				<td>Warehouse Code</td>
				<td><input type="text" name="warehouseCode" value="<?=ifSet($_GET['warehouseCode'])?>"></td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					<select name="status[]" multiple size=3>
					<?php
						foreach(getAttributes('itsm', 'post') as $attribute)
						{
							?>
							<option value="<?=$attribute->getId()?>"<?=(in_array($attribute->getId(), $_GET['status']) ? " selected" : "")?>><?=$attribute->getName()?></option>
							<?php
						}
					?>
					</select>
				</td>
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