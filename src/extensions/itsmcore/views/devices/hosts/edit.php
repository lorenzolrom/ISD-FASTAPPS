<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['h']))
	{
		$host = new itsmcore\Host($_GET['h']);
		
		if($host->load())
		{
			$currentAsset = new itsmcore\Asset($host->getAsset());
			$currentAsset->load();
			
			if(!empty($_POST))
			{
				$save = $host->save($_POST);
				
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "devices/hosts/view?h=" . $host->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['assetTag'] = $currentAsset->getAssetTag();
			$_POST['ipAddress'] = $host->getIpAddress();
			$_POST['macAddress'] = $host->getMacAddress();
			$_POST['systemName'] = $host->getSystemName();
			$_POST['systemCPU'] = $host->getSystemCPU();
			$_POST['systemRAM'] = $host->getSystemRAM();
			$_POST['systemOS'] = $host->getSystemOS();
			$_POST['systemDomain'] = $host->getSystemDomain();
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="host" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>devices/hosts/view?h=<?=$host->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/hostform.php");
		}
		else
			throw new AppException("Host Is Invalid", "P04");
	}
	else
		throw new AppException("Host Not Defined", "P03");
?>