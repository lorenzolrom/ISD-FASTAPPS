<?php
	use itsmcore as itsmcore;
	
	if(!isset($_GET['function']))
		throw new AppException("Function Not Defined", "P03");
	
	if(isset($_GET['po']))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		if($purchaseOrder->loadFromNumber($_GET['po']))
		{
			if($_GET['function'] == "send") // Send Purchase Order to Vendor
			{
				// Check for sent, received, canceled
				if($purchaseOrder->getReceived() == 1)
					throw new AppException("Purchase Order Has Been Received", "P04");
			
				if($purchaseOrder->getCanceled() == 1)
					throw new AppException("Purchase Order Has Been Canceled", "P04");
			
				if($purchaseOrder->getSent() == 1)
					throw new AppException("Purchase Order Has Been Sent", "P04");
				
				if($purchaseOrder->send())
				{
					header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Purchase Order Sent");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Send Purchase Order";
			}
			else if($_GET['function'] == "cancel") // Cancel Purchase Order
			{
				if($purchaseOrder->getReceived() == 1)
					throw new AppException("Purchase Order Has Been Received", "P04");
			
				if($purchaseOrder->getCanceled() == 1)
					throw new AppException("Purchase Order Has Been Canceled", "P04");
				
				if($purchaseOrder->cancel())
				{
					header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Purchase Order Canceled");
					exit();
				}
				else
					$faSystemErrors[] = "Failed To Cancel Purchase Order";
			}
			else if($_GET['function'] == "receive") // Receive Purchase Order
			{
				if(!empty($_POST['receivedIn']))
				{
					if($_POST['receivedIn'] == "full") // Generate all assets
					{
						// Validation
						
						$validator = new Validator();
						
						if(!ifSet($_POST['receiveDate']) OR !$validator->validDate($_POST['receiveDate']))
							$faSystemErrors[] = "Actual Receive Date Is Invalid";
						
						if(empty($faSystemErrors))
						{
							$fullAttribute = new Attribute();
							if(!$fullAttribute->loadFromCode('itsm', 'post', 'rcvf'))
								throw new AppException("Failed To Set P.O. Status", "D09");
							// Set status to Received In Full
							$purchaseOrder->setStatus($fullAttribute->getId());
							
							// Receive P.O.
							if($purchaseOrder->receive($_POST['receiveDate']))
							{
								header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Purchase Order Received");
								exit();
							}
							else
								$faSystemErrors[] = "Failed To Receive Purchase Order";
						}
					}
					else if($_POST['receivedIn'] == "part") // Prompt user to enter in amount for each commodity
					{
						// Process partial order receive
						if(isset($_POST['processPart']))
						{							
							// Set new commodity quantities
							foreach(array_keys($_POST) as $key)
							{
								if(strpos($key, 'quantity-') !== false) // Is a quantity value
								{
									if(ifSet($_POST[$key]) === FALSE)
										$faSystemErrors[] = "Quantity Value Required";
									else if(!ctype_digit($_POST[$key]))
										$faSystemErrors[] = "One Or More Quantities Are Invalid";
									else if($_POST[$key] < 0)
										$faSystemErrors[] = "One Or More Quantities Are Less Than 0";
									
									if(empty($faSystemErrors))
									{
										// Get relationship ID
										$relationshipId = explode('quantity-', $key)[1];
										
										$purchaseOrder->updateQuantity($relationshipId, $_POST[$key]);
									}
									else
										$faSystemErrors[] = "Failed To Receive Purchase Order";
								}
							}
							
							// Validation
						
							$validator = new Validator();
							
							if(!ifSet($_POST['receiveDate']) OR !$validator->validDate($_POST['receiveDate']))
								$faSystemErrors[] = "Actual Receive Date Is Invalid";
							
							if(empty($faSystemErrors))
							{
								// Set status to Received In Part
								$partAttribute = new Attribute();
								if(!$partAttribute->loadFromCode('itsm', 'post', 'rcvp'))
									throw new AppException("Failed To Set P.O. Status", "D09");
								
								$purchaseOrder->setStatus($partAttribute->getId());
								
								// Receive P.O.
								if($purchaseOrder->receive($_POST['receiveDate']))
								{
									header("Location: " . SITE_URI . "inventory/purchaseorders/view?po=" . $purchaseOrder->getNumber() . "&NOTICE=Purchase Order Received");
									exit();
								}
							}
						}
						
						?>
						<div class="button-bar">
							<span class="button form-submit-button" id="quantity" accesskey="n">Next</span>
							<a class="button" href="<?=SITE_URI?>inventory/purchaseorders/view?po=<?=$purchaseOrder->getNumber()?>" accesskey="c">Cancel</a>
						</div>
						<form class="table-form form" id="quantity-form" method="post">
							<input type="hidden" name="receivedIn" value="part">
							<input type="hidden" name="processPart" value="true">
							<input type="hidden" name="receiveDate" value="<?=ifSet($_POST['receiveDate'])?>">
							<h2 class="region-title">Received Quantities</h2>
							<table class="table-display">
								<?php
								foreach($purchaseOrder->getCommodities() as $commodityRow)
								{
									$commodity = new itsmcore\Commodity($commodityRow['commodity']);
									$commodity->load();
									
									?>
									<tr>
										<td><?=$commodity->getCode() . " (" . $commodity->getName() . ")"?> @ <?=$commodityRow['unitCost']?></td>
										<td><input type="number" name="quantity-<?=$commodityRow['id']?>" min="1" value="<?=$commodityRow['quantity']?>"></td>
									</tr>
									<?php
								}
								?>
							</table>
						</form>
						<?php
					}
					else
						throw new AppException("Received In Is Invalid", "P04");
				}
				else
				{
				?>
					<form class="basic-form form" method="post">
						<p>
							<span class="required">Actual Receive Date</span>
							<input type="text" class="date-input" name="receiveDate" value="<?=ifSet($_POST['receiveDate'])?>">
						</p>
						<p>
							<span class="required">Received in </span>
							<select name="receivedIn">
								<option value="full">Full</option>
								<option value="part">Part</option>
							</select>
						</p>
						<input type="submit" class="button" value="Next" accesskey="n">
						<a class="button" href="<?=SITE_URI?>inventory/purchaseorders/view?po=<?=$purchaseOrder->getNumber()?>" accesskey="c">Cancel</a>
					</form>
				<?php
				}
			}
			else
				throw new AppException("Function Is Invalid", "P04");

		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
?>