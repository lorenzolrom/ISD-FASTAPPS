<?php
	if(isset($_GET['t']))
	{
		$attribute = new Attribute($_GET['t']);
		
		if($attribute->load() AND $attribute->getExtension() == "itsm" AND $attribute->getAttributeType() == "asty")
		{
			if(!empty($_POST))
			{
				$validator = new Validator();
				
				if(strlen($_POST['code']) != 4)
					$faSystemErrors[] = "Code Must Be 4 Characters";
				else
				{
					if($_POST['code'] != $attribute->getCode())
					{
						$testCode = new Attribute();

						if($testCode->loadFromCode("itsm", "asty", $_POST['code']))
							$faSystemErrors[] = "Code Already In Use";
					}
				}
				
				if((ifSet($_POST['name']) === FALSE) OR !$validator->validLength($_POST['name'], 1, 30))
					$faSystemErrors[] = "Name Must Be Between 1 And 30 Characters";
				
				if(empty($faSystemErrors))
				{
					$attribute->setCode($_POST['code']);
					$attribute->setName($_POST['name']);
					
					if($attribute->save())
					{
						header("Location: " . SITE_URI . "inventory/settings/assettypes?NOTICE=Changes Saved");
						exit();
					}
					else
						$faSystemErrors[] = "Failed To Update Asset Type";
				}
			}
		
			$_POST['code'] = $attribute->getCode();
			$_POST['name'] = $attribute->getName();
			
			?>
			<div class="button-bar">
				<span id="assettype" class="button form-submit-button" accesskey="s">Save</span>
				<a class="button confirm-button delete-button" href="<?=SITE_URI?>inventory/settings/assettypes/delete?t=<?=$attribute->getId()?>" accesskey="d">Delete</a>
				<span class="button back-button" accesskey="c">Cancel</span>
			</div>
			<?php			
			require_once(dirname(__FILE__) . "/assettypeform.php");
		}
		else
			throw new AppException("Asset Type Is Invalid", "P04");
	}
	else
		throw new AppException("Asset Type Not Defined", "P03");
?>