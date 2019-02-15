<form class="table-form form" method="post" id="host-form">
	<h2 class="region-title">Host Profile</h2>
	<table class="table-display host-display">
		<tr>
			<td class="required">IP Address</td>
			<td><input type="text" name="ipAddress" maxlength=39 value="<?=ifSet($_POST['ipAddress'])?>"></td>
			<td class="required">MAC Address</td>
			<td><input type="text" name="macAddress" maxlength=17 value="<?=ifSet($_POST['macAddress'])?>"></td>
		</tr>
		<tr>
			<td class="required">Asset Tag</td>
			<td><input type="text" name="assetTag" value="<?=ifSet($_POST['assetTag'])?>"></td>
		</tr>
	</table>
	<h2 class="region-title">System Information</h2>
	<table class="table-display host-display">
		<tr>
			<td class="required">System Name</td>
			<td><input type="text" name="systemName" maxlength=64 value="<?=ifSet($_POST['systemName'])?>"></td>
			<td>System CPU</td>
			<td><input type="text" name="systemCPU" maxlength=64 value="<?=ifSet($_POST['systemCPU'])?>"></td>
		</tr>
		<tr>
			<td>System RAM</td>
			<td><input type="text" name="systemRAM" maxlength=64 value="<?=ifSet($_POST['systemRAM'])?>"></td>
			<td>System OS</td>
			<td><input type="text" name="systemOS" maxlength=64 value="<?=ifSet($_POST['systemOS'])?>"></td>
		</tr>
		<tr>
			<td>System Domain</td>
			<td><input type="text" name="systemDomain" maxlength=64 value="<?=ifSet($_POST['systemDomain'])?>"></td>
		</tr>
	</table>
</form>