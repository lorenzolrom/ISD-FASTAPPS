<?php
	use itsmwebmanager as itsmwebmanager;
	
	if(isset($_GET['r']))
	{
		$registrar = new itsmwebmanager\Registrar($_GET['r']);
		
		if($registrar->load())
		{
			if(!empty($_POST))
			{
				$save = $registrar->save($_POST);
				
				if(is_array($save))
					$faSystemErrors = $save;
				else if($save === TRUE)
					exit(header("Location: " . SITE_URI . "webmanager/registrars/view?r=" . $registrar->getId() . "&NOTICE=Changes Saved"));
				else
					$faSystemErrors[] = "Could Not Save Changes";
			}
			
			$_POST['code'] = $registrar->getCode();
			$_POST['name'] = $registrar->getName();
			$_POST['url'] = $registrar->getURL();
			$_POST['phone'] = $registrar->getPhone();
			
			?>
			<div class="button-bar">
				<span class="button form-submit-button" id="registrar" accesskey="s">Save</span>
				<a class="button" href="<?=SITE_URI?>webmanager/registrars/view?r=<?=$registrar->getId()?>" accesskey="c">Cancel</a>
			</div>
			<?php
			require_once(dirname(__FILE__) . "/registrarform.php");
		}
		else
			throw new AppException("Registrar Is Invalid", "P04");
	}
	else
		throw new AppException("Registrar Not Defined", "P03");
?>