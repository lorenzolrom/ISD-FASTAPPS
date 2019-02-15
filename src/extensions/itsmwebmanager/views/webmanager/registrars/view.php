<?php
	use itsmwebmanager as itsmwebmanager;
	
	if(isset($_GET['r']))
	{
		$registrar = new itsmwebmanager\Registrar($_GET['r']);
		
		if($registrar->load())
		{
			$lastModifyUser = new User($registrar->getLastModifyUser());
			$createUser = new User($registrar->getCreateUser());
			
			$lastModifyUser->load();
			$createUser->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?r=<?=$registrar->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?r=<?=$registrar->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Registrar Profile</h2>
			<table class="table-display registrar-display">
				<tr>
					<td>Registrar Code</td>
					<td><?=htmlentities($registrar->getCode())?></td>
					<td>Registrar Name</td>
					<td><?=htmlentities($registrar->getName())?></td>
				</tr>
				<tr>
					<td>Registrar URL</td>
					<td><?=htmlentities($registrar->getURL())?></td>
					<td>Registrar Phone</td>
					<td><?=htmlentities($registrar->getPhone())?></td>
				</tr>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display registrar-display">
				<tr>
					<td>Created</td>
					<td><?=$registrar->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$registrar->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Registrar Is Invalid", "P04");
	}
	else
		throw new AppException("Registrar Not Defined", "P03");
?>