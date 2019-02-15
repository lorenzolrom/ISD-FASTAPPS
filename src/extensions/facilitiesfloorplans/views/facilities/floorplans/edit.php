<?php
	use facilitiesfloorplans as fp;
	use facilitiescore as fc;
	
	$floorplan = null;
	
	if(!isset($_GET['new']))
	{
		if(!isset($_GET['f']))
			throw new AppException("Floorplan Not Defined", "P03");
		
		$floorplan = new fp\Floorplan($_GET['f']);
		if(!$floorplan->load())
			throw new AppException("Floorplan Not Found", "P04");
	}
	
	if(!empty($_POST))
	{
		if($floorplan === NULL AND isset($_GET['new']))
			$floorplan = new fp\Floorplan();
		
		if(isset($_GET['new']))
		{
			$create = $floorplan->create($_POST, $_FILES);
			
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === FALSE)
				$faSystemErrors[] = "Could Not Create Floorplan";
			else
				$notice = "Floorplan Created";
		}
		else
		{
			$save = $floorplan->save($_POST, $_FILES);
			
			if(is_array($save))
				$faSystemErrors = $save;
			else if($save === FALSE)
				$faSystemErrors[] = "Could Not Save Changes";
			else
				$notice = "Changes Saved";
		}
		
		if(isset($notice))
			exit(header("Location: " . getURI() . "/../view?f=" . $floorplan->getId() . "&NOTICE=" . $notice));
	}
	
	if($floorplan !== NULL AND !isset($_GET['new']))
	{
		$building = new fc\Building($floorplan->getBuilding());
		$building->load();
		
		$_POST['buildingCode'] = $building->getCode();
		$_POST['buildingName'] = $building->getName();
		$_POST['floor'] = $floorplan->getFloor();
	}
?>
<div class="button-bar">
	<span class="button form-submit-button" id="floorplan" accesskey="s">Save</span>
	<?php
		if(isset($_GET['new']))
		{
		?>
		<span class="button back-button">Cancel</span>
		<?php
		}
		else
		{
		?>
		<a class="button" href="<?=getURI()?>/../view?f=<?=$_GET['f']?>">Cancel</a>
		<?php
		}
	?>
</div>
<form class="table-form form" method="post" id="floorplan-form" enctype="multipart/form-data">
	<h2 class="region-title">Floorplan Profile</h2>
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
			<td class="required">Floor Name</td>
			<td><input type="text" name="floor" maxlength="32" value="<?=htmlentities(ifSet($_POST['floor']))?>"></td>
			<td class="required">Floorplan Image</td>
			<td><input type="file" name="image"></td>
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