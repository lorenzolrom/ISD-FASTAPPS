<?php
	use itsmcore as itsmcore;
	
	if(isset($_GET['po']))
	{
		$purchaseOrder = new itsmcore\PurchaseOrder();
		
		if($purchaseOrder->loadFromNumber($_GET['po']))
		{
			// Check for sent, received, canceled
			if($purchaseOrder->getReceived() == 1)
				throw new AppException("Purchase Order Has Been Received", "P04");
			
			if($purchaseOrder->getCanceled() == 1)
				throw new AppException("Purchase Order Has Been Canceled", "P05");
			
			if($purchaseOrder->getSent() == 1)
				throw new AppException("Purchase Order Has Been Sent", "P04");
			
			if($purchaseOrder->delete())
			{
				exit(header("Location: " . SITE_URI . "inventory/purchaseorders?NOTICE=Purchase Order Deleted"));
			}
			else
				$faSystemErrors[] = "Could Not Delete Purchase Order";
		}
		else
			throw new AppException("Purchase Order Is Invalid", "P04");
	}
	else
		throw new AppException("Purchase Order Not Defined", "P03");
