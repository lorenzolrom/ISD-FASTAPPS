<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			// Check if already received
			if($returnOrder->getReceived() == 1)
				throw new AppException("Purchase Order Has Been Received", "P04");
			
			if($returnOrder->getCanceled() == 1)
				throw new AppException("Purchase Order Has Been Canceled", "P05");
			
			if($returnOrder->getSent() == 1)
				throw new AppException("Purchase Order Has Been Sent", "P04");
			
			if($_GET['function'] == "add")
			{
				if(!empty($_POST))
				{
					/////
					// Validation
					/////
					
					// Asset
					$asset = new itsmcore\Asset();
					
					if((ifSet($_POST['assetTag']) === FALSE) OR !$asset->loadFromAssetTag($_POST['assetTag']))
						$faSystemErrors[] = "Asset Not Found";
					else if($asset->getReturnOrder() !== FALSE)
						$faSystemErrors[] = "Asset Already On Open Return Order";
					
					if(empty($faSystemErrors))
					{
						if($returnOrder->addAsset($asset->getId()))
						{
							?>
							<script>
								window.opener.location.reload();
								window.close();
							</script>
							<?php
						}
						else
							$faSystemErrors[] = "Failed To Add Asset";
					}
				}
				
				?>
				<h2 class="region-title">Add Asset</h2>
				<form class="basic-form form" method="post">
					<p>
						<span class="required">Asset Tag</span>
						<input id="assetTag" type="text" name="assetTag" value="<?=ifSet($_POST['assetTag'])?>">
					</p>
					<input type="submit" class="button" value="Add" accesskey="a">
					<input type="button" class="button window-close-button" value="Cancel" accesskey="c">
				</form>
				<?php
			}
			else if($_GET['function'] == 'remove' AND isset($_GET['id']))
			{
				$returnOrder->removeAsset($_GET['id']);
				?>
				<script>
					window.opener.location.reload();
					window.close();
				</script>
				<?php
			}
			else
				throw new AppException("Function Is Invalid", "P04");
		}
		else
			throw new AppException("Return Order Is Invalid", "P04");
	}
	else
		throw new AppException("Return Order Not Defined", "P03");
?>