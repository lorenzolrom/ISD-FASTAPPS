<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['l']))
	{
		$location = new facilitiescore\Location($_GET['l']);
		
		if($location->load())
		{
			if(!empty($_POST))
			{
				$save = $location->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "facilities/locations/view?l=" . $location->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$building = new facilitiescore\Building($location->getBuilding());
			$building->load();
			
			$_POST['buildingCode'] = $building->getCode();
			$_POST['code'] = $location->getCode();
			$_POST['name'] = $location->getName();
			$_POST['buildingName'] = $building->getName();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="location" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>facilities/locations/view?l=<?=$location->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/locationform.php");
		}
		else
			throw new AppException("Location Is Invalid", "P04");
	}
	else
		throw new AppException("Location Not Defined", "P03");
?>