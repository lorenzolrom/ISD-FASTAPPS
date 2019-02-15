<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['w']))
	{
		$warehouse = new itsmcore\Warehouse($_GET['w']);
		
		if($warehouse->load())
		{
			$assets = $warehouse->getAssets();
			
			if(sizeof($assets) == 0) // If no assets, close and re-direct
			{
				if($warehouse->close())
				{
					header("Location: " . SITE_URI . "inventory/settings/warehouses?NOTICE=Warehouse Closed");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Close Warehouse";
			}
			else
			{
				if(!empty($_POST['warehouse']))
				{
					$newWarehouse = new itsmcore\Warehouse($_POST['warehouse']);
					
					if($newWarehouse->load())
					{						
						if($warehouse->close($newWarehouse->getId()))
						{							
							$conn->commit();
							header("Location: " . SITE_URI . "inventory/settings/warehouses?NOTICE=Warehouse Closed");
							exit();
						}
					}
					else
						$faSystemErrors[] = "Receiving Warehouse Not Found";
				}
				
				?>
				<p>Warehouse <?=$warehouse->getCode() . "-" . $warehouse->getName()?> has inventory.  Inventory must be transfered to another warehouse.</p>
				<form class="basic-form form" method="post">
					<p>
						<span>Receiving Warehouse</span>
						<select name="warehouse">
						<?php
							foreach(itsmcore\getWarehouses() as $newWarehouse)
							{
								?>
								<option value="<?=$newWarehouse->getId()?>"><?=$newWarehouse->getCode() . "-" . $newWarehouse->getName()?></option>
								<?php
							}
						?>
						</select>
					</p>
					<input class="button" type="submit" value="Transfer" accesskey="t">
				</form>
				<?php
			}
		}
		else
			throw new AppException("Warehouse Is Invalid", "P04");
	}
	else
		throw new AppException("Warehouse Not Defined", "P03");
?>