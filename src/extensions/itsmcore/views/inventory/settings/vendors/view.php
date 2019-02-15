<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['v']))
	{
		$vendor = new itsmcore\Vendor($_GET['v']);
		
		if($vendor->load())
		{
			$lastModifyUser = new \User($vendor->getLastModifyUser());
			$lastModifyUser->load();
			
			$createUser = new \User($vendor->getCreateUser());
			$createUser->load();
			?>
			<div class="button-bar">
				<a class="button" href="<?=getURI()?>/../edit?v=<?=$vendor->getId()?>" accesskey="e">Edit</a>
				<a class="button confirm-button delete-button" href="<?=getURI()?>/../delete?v=<?=$vendor->getId()?>" accesskey="d">Delete</a>
				<a class="button" href="<?=getURI()?>/../new" accesskey="c">Create</a>
			</div>
			<h2 class="region-title">Vendor Profile</h2>
			<table class="table-display">
				<tr>
					<td>Vendor Code</td>
					<td><?=htmlentities($vendor->getCode())?></td>
					<td>Vendor Name</td>
					<td><?=htmlentities($vendor->getName())?></td>
				</tr>
			</table>
			<h2 class="region-title">Contact Information</h2>
			<table class="table-display">
				<tr>
					<td>Street Address</td>
					<td><?=htmlentities($vendor->getStreetAddress())?></td>
					<td>City</td>
					<td><?=htmlentities($vendor->getCity())?></td>
				</tr>
				<tr>
					<td>State</td>
					<td><?=htmlentities($vendor->getState())?></td>
					<td>Zip Code</td>
					<td><?=htmlentities($vendor->getZipCode())?></td>
				</tr>
				<tr>
					<td>Phone</td>
					<td><?=htmlentities($vendor->getPhone())?></td>
					<td>Fax</td>
					<td><?=htmlentities($vendor->getFax())?></td>
				</tr>
			</table>
			<h2 class="region-title">Additional Details</h2>
			<table class="table-display building-display">
				<tr>
					<td>Created</td>
					<td><?=$vendor->getCreateDate()?></td>
					<td>By</td>
					<td><?=$createUser->getUsername()?></td>
				</tr>
				<tr>
					<td>Last Modified</td>
					<td><?=$vendor->getLastModifyDate()?></td>
					<td>By</td>
					<td><?=$lastModifyUser->getUsername()?></td>
				</tr>
			</table>
			<?php
		}
		else
			throw new AppException("Vendor Is Invalid", "P04");
	}
	else
		throw new AppException("Vendor Not Defined", "P03");
?>