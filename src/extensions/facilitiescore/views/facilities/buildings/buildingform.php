<form class="table-form form" method="post" id="building-form">
	<h2 class="region-title">Building Profile</h2>
	<table class="table-display building-display">
		<tr>
			<td class="required">Building Code</td>
			<td><input type="text" name="code" maxlength="32" value="<?=htmlentities(ifSet($_POST['code']))?>"></td>
			<td class="required">Building Name</td>
			<td><input type="text" name="name" maxlength="64" value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
		</tr>
		<tr>
			<td class="required">Street Address</td>
			<td><input type="text" name="streetAddress" value="<?=htmlentities(ifSet($_POST['streetAddress']))?>"></td>
			<td class="required">City</td>
			<td><input type="text" name="city" value="<?=htmlentities(ifSet($_POST['city']))?>"></td>
		</tr>
		<tr>
			<td class="required">State</td>
			<td><input type="text" name="state" maxlength="2" value="<?=htmlentities(ifSet($_POST['state']))?>"></td>
			<td class="required">Zip Code</td>
			<td><input type="text" name="zipCode" maxlength=5 value="<?=htmlentities(ifSet($_POST['zipCode']))?>"></td>
		</tr>
	</table>
</form>