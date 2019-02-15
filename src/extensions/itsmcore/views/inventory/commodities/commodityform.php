<form class="table-form form" method="post" id="commodity-form">
	<h2 class="region-title">Commodity Profile</h2>
	<table class="table-display commodity-display">
		<tr>
			<td class="required">Commodity Code</td>
			<td><input type="text" autocomplete="off" name="code" maxlength=32 value="<?=htmlentities(ifSet($_POST['code']))?>"></td>
			<td class="required">Commodity Name</td>
			<td><input type="text" autocomplete="off" name="name" maxlength=64 value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
		</tr>
		<tr>
			<td class="required">Commodity Type</td>
			<td>
				<select name="commodityType">
					<option value="">No Choice</option>
				<?php
				foreach(getAttributes('itsm', 'coty') as $attribute)
				{
					?>
					<option value="<?=$attribute->getId()?>"<?=(ifSet($_POST['commodityType']) == $attribute->getId() ? " selected" : "")?>><?=htmlentities($attribute->getName())?></option>
					<?php
				}
				?>
				</select>
			</td>
			<td class="required">Asset Type</td>
			<td>
				<select name="assetType">
					<option value="">No Choice</option>
				<?php
				foreach(getAttributes('itsm', 'asty') as $attribute)
				{
					?>
					<option value="<?=$attribute->getId()?>"<?=(ifSet($_POST['assetType']) == $attribute->getId() ? " selected" : "")?>><?=htmlentities($attribute->getName())?></option>
					<?php
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Manufacturer</td>
			<td><input type="text" autocomplete="off" name="manufacturer" value="<?=htmlentities(ifSet($_POST['manufacturer']))?>"></td>
			<td class="required">Model</td>
			<td><input type="text" autocomplete="off" name="model" value="<?=htmlentities(ifSet($_POST['model']))?>"></td>
		</tr>
	</table>
	<h2 class="region-title">Financial Information</h2>
	<table class="table-display commodity-display">
		<tr>
			<td class="required">Unit Cost</td>
			<td><input type="text" autocomplete="off" name="unitCost" value="<?=ifSet($_POST['unitCost'])?>"></td>
		</tr>
	</table>
</form>