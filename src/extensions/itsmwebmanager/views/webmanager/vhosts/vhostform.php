<form class="table-form form" method="post" id="vhost-form">
	<h2 class="region-title">VHost Profile</h2>
	<table class="table-display vhost-display">
		<tr>
			<td class="required">Subdomain</td>
			<td><input type="text" name="subdomain" value="<?=ifSet($_POST['subdomain'])?>"></td>
			<td class="required">Domain</td>
			<td><input type="text" name="domain" value="<?=ifSet($_POST['domain'])?>"></td>
		</tr>
		<tr>
			<td class="required">Name</td>
			<td><input type="text" name="name" maxlength=64 value="<?=ifSet($_POST['name'])?>"></td>
			<td class="required">Renew Cost</td>
			<td><input type="text" name="renewCost" value="<?=ifSet($_POST['renewCost'])?>"></td>
		</tr>
		<tr>
			<td class="required">Host I.P. Address</td>
			<td><input type="text" name="hostIp" value="<?=ifSet($_POST['hostIp'])?>"></td>
			<td class="required">Status</td>
			<td>
				<select name="status">
				<?php
					foreach(getAttributes('itsm', 'wdns') as $attribute)
					{
						?>
						<option value="<?=$attribute->getId()?>"<?=(ifSet($_POST['status']) == $attribute->getId() ? " selected" : "")?>><?=$attribute->getName()?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="required">Date Registered</td>
			<td><input type="text" class="date-input" name="registerDate" value="<?=ifSet($_POST['registerDate'])?>"></td>
			<td>Date Expires</td>
			<td><input type="text" class="date-input" name="expireDate" value="<?=ifSet($_POST['expireDate'])?>"></td>
		</tr>
	</table>
	<h2 class="region-title">Registrar Details</h2>
	<table class="table-display vhost-display">
		<tr>
			<td class="required">Registrar Code</td>
			<td><input id="registrarCode" type="text" name="registrarCode" value="<?=ifSet($_POST['registrarCode'])?>"></td>
			<td>Registrar Name</td>
			<td><input id="registrarName" type="text" name="registrarName" readonly value="<?=ifSet($_POST['registrarName'])?>"></td>
		<tr>
	</table>
	<h2 class="region-title">Additional Details</h2>
	<table class="table-display vhost-display">
		<tr>
			<td>Notes</td>
			<td colspan=3><textarea name="notes"><?=ifSet($_POST['notes'])?></textarea></td>
		</tr>
	</table>
</form>
<script>
	// Update form fields based on selected value
	function ITSMWebManager_VHosts_updateRegistrarDetails()
	{
		$.ajax({
			url: '<?=SITE_URI?>webmanager/vhosts/api?registrar=' + $('#registrarCode').val(),
			type:'GET',
			success: function(data)
			{
				var registrarData = JSON.parse($(data).find('#encoded-data').html());
				
				$('#registrarName').val(registrarData.name);
			}
		});
	}
	
	$(document).ready(function(){
		// Add listener for code change
		$("#registrarCode").change(function(){ITSMWebManager_VHosts_updateRegistrarDetails()});
		
		// Generate autocomplete list
		$.ajax({
			url: '<?=SITE_URI?>webmanager/vhosts/api?registrarCodes',
			type: 'GET',
			success: function(data)
			{
				var registrarCodes = JSON.parse($(data).find('#encoded-data').html());
				$("#registrarCode").autocomplete({
					source: registrarCodes,
					select: function(e, ui)
					{
						$('#registrarCode').val(ui.item.value);
						ITSMWebManager_VHosts_updateRegistrarDetails()
					},
					change: function(e, ui)
					{
						ITSMWebManager_VHosts_updateRegistrarDetails()
					}
				});
			}
		});
	});
</script>