<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['w']))
	{
		$warehouse = new itsmcore\Warehouse($_GET['w']);
		
		if($warehouse->load())
		{
			$lastModifyUser = new \User($warehouse->getLastModifyUser());
			$lastModifyUser->load();
			
			$createUser = new \User($warehouse->getCreateUser());
			$createUser->load();
			
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?w=<?=$warehouse->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../close?w=<?=$warehouse->getId()?>" accesskey="l">Close</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Warehouse Profile</h2>
			<table class="table-display warehouse-display">
				<tr>
					<td>Warehouse Code</td>
					<td><?=htmlentities($warehouse->getCode())?></td>
					<td>Warehouse Name</td>
					<td><?=htmlentities($warehouse->getName())?></td>
				</tr>
			</table>
			
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$warehouse->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$warehouse->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Warehouse Is Invalid", "P04");
	}
	else
		throw new AppException("Warehouse Not Defined", "P03");
?>