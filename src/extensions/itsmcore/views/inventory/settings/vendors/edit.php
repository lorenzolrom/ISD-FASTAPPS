<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['v']))
	{
		$vendor = new itsmcore\Vendor($_GET['v']);
		
		if($vendor->load())
		{
			if(!empty($_POST))
			{
				$save = $vendor->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/settings/vendors/view?v=" . $vendor->getId() . "&NOTICE=Changed Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['code'] = $vendor->getCode();
			$_POST['name'] = $vendor->getName();
			$_POST['streetAddress'] = $vendor->getStreetAddress();
			$_POST['city'] = $vendor->getCity();
			$_POST['state'] = $vendor->getState();
			$_POST['zipCode'] = $vendor->getZipCode();
			$_POST['phone'] = $vendor->getPhone();
			$_POST['fax'] = $vendor->getFax();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="vendor" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>inventory/settings/vendors/view?v=<?=$vendor->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/vendorform.php");
		}
		else
			throw new AppException("Vendor Is Invalid", "P04");
	}
	else
		throw new AppException("Vendor Not Defined", "P03");
?>