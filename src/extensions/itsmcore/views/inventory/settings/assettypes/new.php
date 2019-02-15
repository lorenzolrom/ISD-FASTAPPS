<?php
	if(!empty($_POST))
	{
		$validator = new Validator();
		
		if(strlen($_POST['code']) != 4)
			$faSystemErrors[] = "Code Must Be 4 Characters";
		else
		{
			$testCode = new Attribute();

			if($testCode->loadFromCode("itsm", "asty", $_POST['code']))
				$faSystemErrors[] = "Code Already In Use";
		}
		
		if((ifSet($_POST['name']) === FALSE) OR !$validator->validLength($_POST['name'], 1, 30))
			$faSystemErrors[] = "Name Must Be Between 1 And 30 Characters";
		
		if(empty($faSystemErrors))
		{
			$attribute = new Attribute();
			$attribute->setExtension("itsm");
			$attribute->setAttributeType("asty");
			$attribute->setCode($_POST['code']);
			$attribute->setName($_POST['name']);
			
			if($attribute->create())
			{
				header("Location: " . SITE_URI . "inventory/settings/assettypes?NOTICE=Asset Type Created");
				exit();
			}
			else
				$faSystemErrors[] = "Failed To Create Asset Type";
		}
	}
	?>
	<div class="button-bar">
		<span id="assettype" class="button form-submit-button" accesskey="s">Save</span>
		<span class="button back-button" accesskey="c">Cancel</span>
	</div>
	<?php
	require_once(dirname(__FILE__) . "/assettypeform.php");
?>