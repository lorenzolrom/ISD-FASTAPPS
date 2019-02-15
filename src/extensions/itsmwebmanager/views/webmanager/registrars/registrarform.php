<form class="table-form form" method="post" id="registrar-form">
	<h2 class="region-title">Registrar Profile</h2>
	<table class="table-display commodity-display">
		<tr>
			<td class="required">Registrar Code</td>
			<td><input type="text" autocomplete="off" name="code" maxlength=32 value="<?=htmlentities(ifSet($_POST['code']))?>"></td>
			<td class="required">Registrar Name</td>
			<td><input type="text" autocomplete="off" name="name" value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
		</tr>
		<tr>
			<td class="required">Registrar URL</td>
			<td><input type="text" autocomplete="off" name="url" value="<?=htmlentities(ifSet($_POST['url']))?>"></td>
			<td class="required">Registrar Phone</td>
			<td><input type="text" autocomplete="off" name="phone" maxlength=20 value="<?=htmlentities(ifSet($_POST['phone']))?>"></td>
		</tr>
	</table>
</form>