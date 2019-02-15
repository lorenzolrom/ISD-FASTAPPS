<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			// Check if already received
			if($returnOrder->getReceived() == 1)
				throw new AppException("Return Order Has Been Received", "P04");
			
			if($returnOrder->getCanceled() == 1)
				throw new AppException("Return Order Has Been Canceled", "P05");
			
			if($returnOrder->getSent() == 1)
				throw new AppException("Return Order Has Been Sent", "P04");
			
			if($_GET['function'] == "add")
			{
				if(!empty($_POST))
				{
					/////
					// Validation
					/////
					
					// Cost
					if(strlen($_POST['cost']) == 0)
						$faSystemErrors[] = "Cost Required";
					else if(!is_numeric($_POST['cost']))
						$faSystemErrors[] = "Cost Must Be Numeric";
					else if($_POST['cost'] < 0)
						$faSystemErrors[] = "Cost Must Be Positive";
					
					if(empty($faSystemErrors))
					{
						if($returnOrder->addCostItem($_POST['cost'], $_POST['notes']))
						{
							?>
							<script>
								window.opener.location.reload();
								window.close();
							</script>
							<?php
						}
						else
							$faSystemErrors[] = "Failed To Add Cost Item";
					}
				}
				
				?>
				<h2 class="region-title">Add Cost Item</h2>
				<form class="basic-form form" method="post">
					<p>
						<span class="required">Unit Cost</span>
						<input type="text" name="cost" value="<?=ifSet($_POST['cost'])?>">
					</p>
					<p>
						<span>Notes</span>
						<input type="text" name="notes" value="<?=ifSet($_POST['notes'])?>">
					</p>
					<input type="submit" class="button" value="Add" accesskey="a">
					<input type="button" class="button window-close-button" value="Cancel" accesskey="c">
				</form>
				<?php
			}
			else if($_GET['function'] == 'remove' AND isset($_GET['id']))
			{
				$returnOrder->removeCostItem($_GET['id']);
				?>
				<script>
					window.opener.location.reload();
					window.close();
				</script>
				<?php
			}
			else
				throw new AppException("Function Is Invalid", "P04");
		}
		else
			throw new AppException("Return Order Is Invalid", "P04");
	}
	else
		throw new AppException("Return Order Not Defined", "P03");
?>