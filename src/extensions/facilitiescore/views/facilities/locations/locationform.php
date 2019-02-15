<form class="table-form form" method="post" id="location-form">
	<h2 class="region-title">Location Profile</h2>
	<table class="table-display location-display">
		<tr>
			<td class="required">Building Code</td>
			<td>
				<input id="buildingCode" type="text" name="buildingCode" value="<?=htmlentities(ifSet($_POST['buildingCode']))?>">
			</td>
			<td>Building Name</td>
			<td>
				<input readonly id="buildingName" type="text" name="buildingName" value="<?=htmlentities(ifSet($_POST['buildingName']))?>">
			</td>
		</tr>
		<tr>
			<td class="required">Location Code</td>
			<td><input type="text" name="code" maxlength="32" value="<?=htmlentities(ifSet($_POST['code']))?>"></td>
			<td class="required">Location Name</td>
			<td><input type="text" name="name" maxlength="64" value="<?=htmlentities(ifSet($_POST['name']))?>"></td>
		</tr>
	</table>
</form>
<script>
	// Update form fields based on selected value
	function FacilitiesCore_Locations_updateLocationDetails()
	{
		$.ajax({
			url: '<?=SITE_URI?>facilities/buildings/api?building=' + $('#buildingCode').val(),
			type:'GET',
			success: function(data)
			{
				var buildingData = JSON.parse($(data).find('#encoded-data').html());
				
				$('#buildingName').val(buildingData.name);
			}
		});
	}
	
	$(document).ready(function(){
		// Add listener for code change
		$("#buildingCode").change(function(){FacilitiesCore_Locations_updateLocationDetails()});
		
		// Generate autocomplete list
		$.ajax({
			url: '<?=SITE_URI?>facilities/buildings/api?buildingCodes',
			type: 'GET',
			success: function(data)
			{
				var buildingCodes = JSON.parse($(data).find('#encoded-data').html());
				$("#buildingCode").autocomplete({
					source: buildingCodes,
					select: function(e, ui)
					{
						$('#buildingCode').val(ui.item.value);
						FacilitiesCore_Locations_updateLocationDetails()
					},
					change: function(e, ui)
					{
						FacilitiesCore_Locations_updateLocationDetails()
					}
				});
			}
		});
	});
</script>