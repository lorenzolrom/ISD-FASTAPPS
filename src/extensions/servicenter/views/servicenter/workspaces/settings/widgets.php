<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
	
	if(!isset($_GET['f']))
	{
		$results['type'] = "table";
		$results['linkColumn'] = 2;
		$results['href'] = SITE_URI . "servicenter/workspaces/settings/widgets?w=" . $workspace->getId() . "&f=remove&p=";
		$results['head'] = ['Position', 'Name', ''];
		$results['widths'] = ["10px", "", "10px"];
		$results['align'] = ["right", "left", "center"];
		$results['data'] = [];
		$results['refs'] = [];
		
		foreach($workspace->getWidgets() as $widget)
		{			
			$results['refs'][] = [$widget[0]];
			$results['data'][] = [$widget[0], $widget[1], "REMOVE"];
		}		
		?>
		<div class="button-bar">
				<a class="button" href="<?=getURI()?>?w=<?=ifSet($_GET['w'])?>&f=add" accesskey="a">Add</a>
				<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings?w=<?=$workspace->getId()?>" accesskey="b">Back</a>
			</div>
			<h2 class="region-title">Widgets Settings for Workspace: <?=$workspace->getName()?></h2>
			<div class="region" id="widgets">
				<span class="red-message">NO DATA FOUND</span>
			</div>
		<?php
		if(isset($results['data']) AND !empty($results['data']))
		{
			?>
			<script>showResults('widgets', <?=json_encode($results)?>, <?=RESULTS_PER_PAGE?>)</script>
			<?php
		}
	}
	else if($_GET['f'] == "add")
	{
		if(!empty($_POST))
		{
			/////
			// VALIDATION
			/////
			
			// Position, is positive number
			if(!isset($_POST['position']) OR strlen($_POST['position']) == 0)
				$faSystemErrors[] = "Position Required";
			else if(!ctype_digit($_POST['position']))
				$faSystemErrors[] = "Position Must Be A Positive Integer";
			
			// Widget, is positive number
			if(!isset($_POST['widget']) OR strlen($_POST['widget']) == 0)
				$faSystemErrors[] = "Widget Required";
			else if(!ctype_digit($_POST['widget']))
				$faSystemErrors[] = "Widget Is Not Valid";
			
			if(empty($faSystemErrors))
			{
				if($workspace->addWidget($_POST['position'], $_POST['widget']))
					exit(header("Location: " . getURI() . "?w=" . $workspace->getId() . "&NOTICE=Widget Added"));
				else
					$faSystemErrors[] = "Could Not Add Widget";
			}
		}
		
		?>
		<div class="button-bar">
			<span id="add" class="button form-submit-button" accesskey="s">Save</span>
			<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings/widgets?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
		</div>
		<h2 class="region-title">Add Widget to Workspace: <?=$workspace->getName()?></h2>
		<form class="basic-form form" method="post" id="add-form">
			<p>
				<span class="required">Position</span>
				<input type="text" name="position" value="<?=ifSet($_POST['position'])?>">
			</p>
			<p>
				<span class="required">Widget</span>
				<select name="widget">
				<?php
					for($i = 0; $i < sizeof($SERVICENTER_WIDGETS); $i++)
					{
						?>
						<option value="<?=$i?>"<?=ifSet($_POST['widget']) == $i ? " selected" : ""?>><?=$SERVICENTER_WIDGETS[$i][0]?></option>
						<?php
					}
				?>
				</select>
			</p>
		</form>
		<?php
	}
	else if($_GET['f'] == "remove")
	{
		if(!isset($_GET['p']))
			throw new AppException("Position Not Defined", "P03");
		
		if($workspace->removeWidget($_GET['p']))
			exit(header("Location: " . getURI() . "?w=" . $workspace->getId() . "&NOTICE=Widget Removed"));
		else
			$faSystemErrors[] = "Could Not Remove Widget";
	}
	else
		throw new AppException("Function Not Found", "P05");
?>