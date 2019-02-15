<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['ro']))
	{
		$returnOrder = new itsmcore\ReturnOrder();
		
		if($returnOrder->loadFromNumber($_GET['ro']))
		{
			// Check for sent, received, canceled
			if($returnOrder->getReceived() == 1)
				throw new AppException("Return Order Has Been Received", "P04");
			
			if($returnOrder->getCanceled() == 1)
				throw new AppException("Return Order Has Been Canceled", "P05");
			
			if($returnOrder->getSent() == 1)
				throw new AppException("Return Order Has Been Sent", "P04");
			
			if($returnOrder->delete())
			{
				header("Location: " . SITE_URI . "inventory/returns?NOTICE=Return Order Deleted");
				exit();
			}
			else
				$faSystemErrors[] = "Failed To Delete Return Order";
		}
		else
			throw new AppException("Return Order Is Invalid", "P04");
	}
	else
		throw new AppException("Return Order Not Defined", "P03");
