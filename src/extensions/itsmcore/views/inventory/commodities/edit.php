<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['c']))
	{
		$commodity = new itsmcore\Commodity($_GET['c']);
		
		if($commodity->load())
		{
			if(!empty($_POST))
			{
				$save = $commodity->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/commodities/view?c=" . $commodity->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			$_POST['code'] = $commodity->getCode();
			$_POST['name'] = $commodity->getName();
			$_POST['commodityType'] = $commodity->getCommodityType();
			$_POST['assetType'] = $commodity->getAssetType();
			$_POST['manufacturer'] = $commodity->getManufacturer();
			$_POST['model'] = $commodity->getModel();
			$_POST['unitCost'] = $commodity->getUnitCost();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="commodity" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>inventory/commodities/view?c=<?=$commodity->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/commodityform.php");
		}
		else
			throw new AppException("Commodity Is Invalid", "P04");
	}
	else
		throw new AppException("Commodity Not Defined", "P03");
?>