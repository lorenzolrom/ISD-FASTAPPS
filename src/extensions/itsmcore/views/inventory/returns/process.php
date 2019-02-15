<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			if($_GET['function'] == "send") // Send Return Order to Vendor
			{
				// Check for sent, received, canceled
				if($returnOrder->getReceived() == 1)
					throw new AppException("Return Order Has Been Received", "P04");
			
				if($returnOrder->getCanceled() == 1)
					throw new AppException("Return Order Has Been Canceled", "P04");
			
				if($returnOrder->getSent() == 1)
					throw new AppException("Return Order Has Been Sent", "P04");
				
				if($returnOrder->send())
				{
					header("Location: " . SITE_URI . "inventory/returns/view?ro=" . $returnOrder->getNumber() . "&NOTICE=Return Order Sent");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Send Return Order";
			}
			else if($_GET['function'] == "cancel") // Cancel Return Order
			{
				if($returnOrder->getReceived() == 1)
					throw new AppException("Return Order Has Been Received", "P04");
			
				if($returnOrder->getCanceled() == 1)
					throw new AppException("Return Order Has Been Canceled", "P04");
				
				if($returnOrder->cancel())
				{
					header("Location: " . SITE_URI . "inventory/returns/view?ro=" . $returnOrder->getNumber() . "&NOTICE=Return Order Canceled");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Cancel Return Order";
			}
			else if($_GET['function'] == "receive") // Receive Return Order
			{
				if($returnOrder->getCanceled() == 1)
					throw new AppException("Return Order Has Been Canceled", "P04");
				
				if(!empty($_POST))
				{
					$validator = new Validator();
						
					if(!ifSet($_POST['receiveDate']) OR !$validator->validDate($_POST['receiveDate']))
						$faSystemErrors[] = "Actual Receive Date Is Invalid";
					
					if(empty($faSystemErrors))
					{
						if($returnOrder->receive($_POST['receiveDate']))
						{
							header("Location: " . SITE_URI . "inventory/returns/view?ro=" . $returnOrder->getNumber() . "&NOTICE=Return Order Received");
							exit();
						}
						else
							$faSystemErrors[] = "Failed To Receive Return Order";
					}
				}
				?>
					<form class="basic-form form" method="post">
						<p>
							<span>Actual Receive Date</span>
							<input type="text" class="date-input" name="receiveDate" value="<?=ifSet($_POST['receiveDate'])?>">
						</p>
						<input type="submit" class="button" value="Next" accesskey="n">
						<a class="button" href="<?=SITE_URI?>inventory/returns/view?ro=<?=$returnOrder->getNumber()?>" accesskey="c">Cancel</a>
					</form>
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