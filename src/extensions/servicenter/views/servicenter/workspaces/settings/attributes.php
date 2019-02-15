<?php
	use servicenter as sc;
	
	if(!isset($_GET['w']))
		throw new AppException("Workspace Not Defined", "P03");
	
	$workspace =  new sc\Workspace($_GET['w']);
	if(!$workspace->load())
		throw new AppException("Workspace Is Invalid", "P04");
	
	if(!isset($_GET['f']))
	{
		$types = ['seve', 'cate', 'type', 'tsta', 'tclc'];
		$results = [];
		
		foreach($types as $type)
		{
			$results[$type]['type'] = "table";
			$results[$type]['linkColumn'] = 3;
			$results[$type]['href'] = SITE_URI . "servicenter/workspaces/settings/attributes?w=" . $workspace->getId() . "&f=remove&a=";
			$results[$type]['head'] = ['Code', 'Name', 'Default', ''];
			$results[$type]['widths'] = ["10px", "", "10px", "10px"];
			$results[$type]['align'] = ["right", "left", "center", "center"];
			$results[$type]['data'] = [];
			$results[$type]['refs'] = [];
			
			foreach($workspace->getAttributes($type) as $a)
			{
				$results[$type]['refs'][] = [$a->getId()];
				$results[$type]['data'][] = [$a->getCode(), $a->getName(), ($a->getDefault() == 1) ? "✔" : "", "REMOVE"];
			}
		}
		
		?>
			<div class="button-bar">
				<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings?w=<?=$workspace->getId()?>" accesskey="b">Back</a>
			</div>
			<h2 class="region-title">Attribute Settings for Workspace: <?=$workspace->getName()?></h2>
			<h2 class="region-title region-expand region-expand-collapsed" id="severity">Severity
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=add&a=seve" class="button-noveil">Add</a>
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=default&a=seve" class="button-noveil">Change Default</a>
			</h2>
			<div class="region" id="severity-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>

			<h2 class="region-title region-expand region-expand-collapsed" id="type">Type
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=add&a=type" class="button-noveil">Add</a>
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=default&a=type" class="button-noveil">Change Default</a>
			</h2>
			<div class="region" id="type-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>

			<h2 class="region-title region-expand region-expand-collapsed" id="category">Category
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=add&a=cate" class="button-noveil">Add</a>
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=default&a=cate" class="button-noveil">Change Default</a>
			</h2>
			<div class="region" id="category-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>

			<h2 class="region-title region-expand region-expand-collapsed" id="status">Status
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=add&a=tsta" class="button-noveil">Add</a>
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=default&a=tsta" class="button-noveil">Change Default</a>
			</h2>
			<div class="region" id="status-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>

			<h2 class="region-title region-expand region-expand-collapsed" id="closurecode">Closure Code
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=add&a=tclc" class="button-noveil">Add</a>
				<a href="<?=getURI()?>?w=<?=$workspace->getId()?>&f=default&a=tclc" class="button-noveil">Change Default</a>
			</h2>
			<div class="region" id="closurecode-region">
				<span class="red-message">NO DATA FOUND</span>
			</div>
			<?php
				if(isset($results['seve']) AND !empty($results['seve']['data']))
				{
					?>
					<script>showResults('severity-region', <?=json_encode($results['seve'])?>, <?=RESULTS_PER_PAGE?>)</script>
					<?php
				}
				if(isset($results['type']) AND !empty($results['type']['data']))
				{
					?>
					<script>showResults('type-region', <?=json_encode($results['type'])?>, <?=RESULTS_PER_PAGE?>)</script>
					<?php
				}
				if(isset($results['cate']) AND !empty($results['cate']['data']))
				{
					?>
					<script>showResults('category-region', <?=json_encode($results['cate'])?>, <?=RESULTS_PER_PAGE?>)</script>
					<?php
				}
				if(isset($results['tsta']) AND !empty($results['tsta']['data']))
				{
					?>
					<script>showResults('status-region', <?=json_encode($results['tsta'])?>, <?=RESULTS_PER_PAGE?>)</script>
					<?php
				}
				if(isset($results['tclc']) AND !empty($results['tclc']['data']))
				{
					?>
					<script>showResults('closurecode-region', <?=json_encode($results['tclc'])?>, <?=RESULTS_PER_PAGE?>)</script>
					<?php
				}
			?>
		<?php
	}
	else if($_GET['f'] == "add")
	{
		if(!isset($_GET['a']))
			throw new AppException("Attribute Type Not Defined", "P03");
		if(!in_array($_GET['a'], ['seve', 'cate', 'type', 'tsta', 'tclc']))
			throw new AppException("Attribute Type Is Invalid", "P04");
		
		if(!empty($_POST))
		{
			$wa = new sc\WorkspaceAttribute();
			$wa->setWorkspace($workspace->getId());
			$wa->setAttributeType($_GET['a']);
			
			$create = $wa->create($_POST);
			
			if(is_array($create))
				$faSystemErrors = $create;
			else if($create === TRUE)
				exit(header("Location: " . SITE_URI . "servicenter/workspaces/settings/attributes?w=" . $workspace->getId() . "&NOTICE=Attribute Created"));
			else
				$faSystemErrors[] = "Could Not Create Attribute";
		}
		
		?>
		<div class="button-bar">
			<span id="add" class="button form-submit-button" accesskey="s">Save</span>
			<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings/attributes?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
		</div>
		<h2 class="region-title">New Attribute (<?=$_GET['a']?>) in Workspace: <?=$workspace->getName()?></h2>
		<form class="basic-form form" method="post" id="add-form">
			<p>
				<span class="required">Code</span>
				<input type="text" maxlength=4 name="code" value="<?=ifSet($_POST['code'])?>">
			</p>
			<p>
				<span class="required">Name</span>
				<input type="text" maxlength=30 name="name" value="<?=ifSet($_POST['name'])?>">
			</p>
			<p>
				<span class="required">Default Code</span>
				<select name="default">
					<option value="0">No</option>
					<option value="1"<?=ifSet($_POST['default']) == 1 ? " selected" : ""?>>Yes</option>
				</select>
			</p>
		</form>
		<?php
	}
	else if($_GET['f'] == "default")
	{
		if(!isset($_GET['a']))
			throw new AppException("Attribute Type Not Defined", "P03");
		if(!in_array($_GET['a'], ['seve', 'cate', 'type', 'tsta', 'tclc']))
			throw new AppException("Attribute Type Is Invalid", "P04");
		
		if(!empty($_POST))
		{
			$a = new sc\WorkspaceAttribute();
			
			if(!$a->loadFromCode($workspace->getId(), $_GET['a'], $_POST['code']))
				$faSystemErrors[] = "Attribute Code Not Found";
			
			if(empty($faSystemErrors))
			{
				if($a->setDefault())
					exit(header("Location: " . SITE_URI . "servicenter/workspaces/settings/attributes?w=" . $workspace->getId() . "&NOTICE=Default Attribute Set"));
				else
					$faSystemErrors[] = "Failed To Set Default";
			}
		}
		
		?>
		<div class="button-bar">
			<span id="default" class="button form-submit-button" accesskey="s">Save</span>
			<a class="button" href="<?=SITE_URI?>servicenter/workspaces/settings/attributes?w=<?=$workspace->getId()?>" accesskey="c">Cancel</a>
		</div>
		<h2 class="region-title">Set New Default Attribute (<?=$_GET['a']?>) in Workspace: <?=$workspace->getName()?></h2>
		<form class="basic-form form" method="post" id="default-form">
			<p>
				<span class="required">Code</span>
				<input type="text" maxlength=4 name="code" value="<?=ifSet($_POST['code'])?>">
			</p>
		</form>
		<?php
	}
	else if($_GET['f'] == "remove")
	{
		if(!isset($_GET['a']))
			throw new AppException("Attribute Not Defined", "P03");
		
		$wa = new sc\WorkspaceAttribute($_GET['a']);
		if(!$wa->load())
			throw new AppException("Attribute Is Invalid", "P04");
		
		if($wa->delete())
			exit(header("Location: " . SITE_URI . "servicenter/workspaces/settings/attributes?w=" . $workspace->getId() . "&NOTICE=Attribute Deleted"));
		else
			$faSystemErrors[] = "Could Not Delete Attribute";
	}
	else
		throw new AppException("Function Is Invalid", "P05");
?>