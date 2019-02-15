<form class="table-form form" method="post" id="vendor-form">
	<h2 class="region-title">Vendor Profile</h2>
	<table class="table-display">
		<tr>
			<td class="required">Vendor Code</td>
			<td><input type="text" autocomplete="off" name="code" maxlength="32" value="<?=htmlentities(ifSet($_POST['code']))?>"></td>
			<td class="required">Vendor Name</td>
			<td><input type="text" autocomplete="off" name="name" value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
		</tr>
	</table>
	<h2 class="region-title">Contact Information</h2>
	<table class="table-display">
		<tr>
			<td class="required">Street Address</td>
			<td><input type="text" autocomplete="off" name="streetAddress" value="<?=htmlentities(ifSet($_POST['streetAddress']))?>"></td>
			<td class="required">City</td>
			<td><input type="text" autocomplete="off" name="city" value="<?=htmlentities(ifSet($_POST['city']))?>"></td>
		</tr>
		<tr>
			<td class="required">State</td>
			<td><input type="text" autocomplete="off" maxlength=2 name="state" value="<?=htmlentities(ifSet($_POST['state']))?>"></td>
			<td class="required">Zip Code</td>
			<td><input type="text" autocomplete="off" maxlength=5 name="zipCode" value="<?=htmlentities(ifSet($_POST['zipCode']))?>"></td>
		</tr>
		<tr>
			<td class="required">Phone</td>
			<td><input type="text" autocomplete="off" maxlength=20 name="phone" value="<?=htmlentities(ifSet($_POST['phone']))?>"></td>
			<td>Fax</td>
			<td><input type="text" autocomplete="off" maxlength=20 name="fax" value="<?=htmlentities(ifSet($_POST['fax']))?>"></td>
		</tr>
	</table>
</form>