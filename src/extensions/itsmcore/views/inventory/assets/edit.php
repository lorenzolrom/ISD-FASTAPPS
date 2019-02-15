<?php
	use itsmcore as itsmcore;

	if(isset($_GET['a']))
	{
		$asset = new itsmcore\Asset($_GET['a']);
		
		if($asset->load())
		{
			if($asset->getDiscarded() == 1)
				throw new AppException("Asset Has Been Discarded", "P04");
			
			if(!empty($_POST))
			{
				$save = $asset->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/assets/view?a=" . $asset->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['assetTag'] = $asset->getAssetTag();
			$_POST['serialNumber'] = $asset->getSerialNumber();
			$_POST['manufactureDate'] = $asset->getManufactureDate();
			$_POST['notes'] = $asset->getNotes();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="asset" accesskey="s">Save</span>
				<a href="<?=SITE_URI?>inventory/assets/view?a=<?=$asset->getId()?>" class="button" accesskey="c">Cancel</a>
			</div>
			<form class="table-form form" method="post" id="asset-form">
				<h2 class="region-title">Asset Profile</h2>
				<table class="table-display asset-display">
					<tr>
						<td>Asset #</td>
						<td><input type="text" name="assetTag" value="<?=ifSet($_POST['assetTag'])?>"></td>
						<td>Serial Number</td>
						<td><input type="text" name="serialNumber" maxlength=64 value="<?=htmlentities(ifSet($_POST['serialNumber']))?>"></td>
					</tr>
					<tr>
						<td>Manufacture Date</td>
						<td><input type="text" name="manufactureDate" maxlength=64 value="<?=htmlentities(ifSet($_POST['manufactureDate']))?>"></td>
					</tr>
				</table>
				<h2 class="region-title">Additional Details</h2>
				<table class="table-display asset-display">
					<tr>
						<td>Notes</td>
						<td colspan=3><textarea name="notes"><?=htmlentities(ifSet($_POST['notes']))?></textarea></td>
					</tr>
				</table>
			</form>
			<?php
		}
		else
			throw new AppException("Asset Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Not Defined", "P03");
?>