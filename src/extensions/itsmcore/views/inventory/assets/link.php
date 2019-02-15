<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['a']))
		throw new AppException("Asset Not Defined", "P03");
	
	$asset = new itsmcore\Asset($_GET['a']);
	
	if(!$asset->load())
		throw new AppException("Asset Not Found", "P04");
	
	if(!isset($_GET['f']))
		throw new AppException("Operation Not Defined", "P03");
	
	if($_GET['f'] == "link")
	{
		if(!empty($_POST['link']))
		{
			$link = $asset->setParent($_POST['link']);
			
			if(is_array($link))
				$faSystemErrors = $link;
			else if($link === TRUE)
				exit(header("Location: " . getURI() . "/../view?a=" . $asset->getId() . "&NOTICE=Linked To Parent"));
			else
				$faSystemErrors[] = "Could Not Link To Parent";
		}
		
		?>
		<h2 class="region-title">Link Asset</h2>
		<form class="basic-form form" method="post">
			<p>
				<span class="required">Link To Asset</span>
				<input type="text" name="link" value="<?=ifSet($_POST['link'])?>">
			</p>
			<input type="submit" class="button" value="Link" accesskey="a">
			<a href="<?=getURI()?>/../view?a=<?=$_GET['a']?>" class="button" accesskey="c">Cancel</a>
		</form>
		<?php
	}
	else if($_GET['f'] = "unlink")
	{
		if($asset->unsetParent())
			exit(header("Location: " . getURI() . "/../view?a=" . $asset->getId() . "&NOTICE=Unlinked From Parent"));
		else
			$faSystemErrors[] = "Could Not Unlink From parent";
	}
	else
		throw new AppException("Operation Not Found", "P04");
?>