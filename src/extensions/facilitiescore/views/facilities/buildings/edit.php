<?php
	use facilitiescore as facilitiescore;
	
	if(isset($_GET['b']))
	{
		$building = new facilitiescore\Building($_GET['b']);
		
		if($building->load())
		{
			if(!empty($_POST))
			{
				$save = $building->save($_POST);
				
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "facilities/buildings/view?b=" . $building->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['code'] = $building->getCode();
			$_POST['name'] = $building->getName();
			$_POST['streetAddress'] = $building->getStreetAddress();
			$_POST['city'] = $building->getCity();
			$_POST['state'] = $building->getState();
			$_POST['zipCode'] = $building->getZipCode();
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="building" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>facilities/buildings/view?b=<?=$building->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/buildingform.php");
		}
		else
		{
			throw new AppException("Building Is Invalid", "P04");
		}
	}
	else
	{
		throw new AppException("Building Not Defined", "P03");
	}
?>