<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['w']))
	{
		$warehouse = new itsmcore\Warehouse($_GET['w']);
		
		if($warehouse->load())
		{
			if(!empty($_POST))
			{
				$save = $warehouse->save($_POST);
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "inventory/settings/warehouses/view?w=" . $warehouse->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
		
			$_POST['code'] = $warehouse->getCode();
			$_POST['name'] = $warehouse->getName();
			
			?>
			<div class="button-bar">
				<span id="warehouse" class="button form-submit-button" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>inventory/settings/warehouses/view?w=<?=$warehouse->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php			
			require_once(dirname(__FILE__) . "/warehouseform.php");
		}
		else
			throw new AppException("Warehouse Is Invalid", "P04");
	}
	else
		throw new AppException("Warehouse Not Defined", "P03");
?>