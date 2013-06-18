<?php
class Pixafy_Bevel_Model_Observer extends Varien_Object
{
	//Redirect user to WP site if they are not logged in
	function CheckUser($observer)
	{
		$excludeList = array("loginPost", "register_user", "logoutSuccess", "forgotpassword");
		$event = $observer->getEvent();
		$frontController = Mage::app()->getFrontController();
		$request = $frontController->getRequest();
		if($request->getModuleName() !="admin" && !$event->getData("customer_session")->isLoggedIn() && !in_array($request->getRequestedActionName(), $excludeList))
		{
			//Mage::app()->getResponse()->setRedirect(Mage::helper("bevel/data")->pre_reg_url);
		}
	}
	
	//Functon to set cookie containing popup frequency
	function setFrequency($observer)
	{
		$frequency = Mage::getStoreConfig('bevel/general/popup_frequency');
		$frequencyCookie = "popup_frequency";
		$popupCountCookie = "popup_count";
		if($frequency && !Mage::getModel('core/cookie')->get($frequencyCookie))
		{
			Mage::getModel('core/cookie')->set($frequencyCookie, $frequency, true);
			Mage::getModel('core/cookie')->set($popupCountCookie, 0, true);
		}
	}
	
	//Find customer who registered on the site in the past week
	//Check the creation date
	function sendCustomerEmails()
	{
		$logPath=Mage::getBaseDir('log').DS.'customer_emails.log';
		$dayInSecs = 86400;
		$emailHelper = Mage::helper("bevel/email");
		$pixvoteHelper = Mage::helper("pixvote");
		$now = Mage::getModel('core/date')->timestamp(time());
		$dateFormat = 'Y-m-d H:i:s';
		$today = date($dateFormat, $now);
		$todayTime = strtotime($today);
		$todayDateTime = new DateTime($today);
		$pastDateTime = clone $todayDateTime;
		$pastDateTime->modify('-5 day');
		
		//Begin cron log
		$logText = "------Start Cron------\n\n";
		$logText .= "Crontime: ".$today."\n-------------\n";
		
		
		//Get customers registered in the past 3 days
		$customers =  Mage::getResourceModel('customer/customer_collection')
		->addNameToSelect()
		->addAttributeToSelect(array('how_it_works_email_sent','gift_email_sent'),'left')
		->addFieldToFilter('created_at', array('datetime' => true, 'from' => $pastDateTime->format($dateFormat)))
		->addFieldToFilter('created_at', array('datetime' => true, 'to' => $todayDateTime->format($dateFormat)))
		->addAttributeToFilter(array(
		array(
			'attribute' => 'how_it_works_email_sent',
			'null'        => '',
			),
		array(
			'attribute' => 'gift_email_sent',
			'null'        => '',
			),
		));
		$howItWorksEmailsSent = array();
		$giftEmailsSent = array();
		$query = (string)$customers->getSelect();
		$logText .= "Query: ".$query."\n-------------\n";
		$logText .= "Customers found being created between " .$pastDateTime->format($dateFormat)." and ".$todayDateTime->format($dateFormat).": ".count($customers)."\n-------------\n";
		foreach($customers as $customer)
		{
			$timeDiff = $todayTime - strtotime($customer->getCreatedAt());
			$save = false;
					
			//Check if registered for a day or more and that they have not receved the how it works email yet
			if(!isset($howItWorksEmailsSent[$customer->getEmail()]))
			{
				if($timeDiff >= $dayInSecs && !$customer->getHowItWorksEmailSent())
				{
					$logText .="Sending customer ".$customer->getId()."-".$customer->getName()." how it works email\n-------------\n";
					$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_HOW_IT_WORKS, $customer->getEmail());
					$customer->setHowItWorksEmailSent($pixvoteHelper->VAL_TRUE);
					$save = true;
					$howItWorksEmailsSent[$customer->getEmail()] = true;
				}
			}
			
			//Check if registered for a day or more and that they have not receved the gift certificate email yet
			if(!isset($giftEmailsSent[$customer->getEmail()]))
			{
				if($timeDiff >= ($dayInSecs * 3)&& !$customer->getGiftEmailSent())
				{
					$logText .="Sending customer ".$customer->getId()."-".$customer->getName()." gift certificate email\n-------------\n";
					$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_GIFT_CERTIFICATE, $customer->getEmail());
					$customer->setGiftEmailSent($pixvoteHelper->VAL_TRUE);
					$save = true;
					$giftEmailsSent[$customer->getEmail()] = true;
				}
			}
			if($save)
			{
				try
				{
					$customer->save();
				}
				catch(Exception $e)
				{
					$logText .="ERROR: Could not save ".$customer->getId()."-".$customer->getName().": ".$e->getMessage()."\n-------------\n";
				}
			}
		}
		//$customers->save();
		$logText .= "------End cron------\n\n";
		$fh = fopen($logPath, 'a') ;
		fwrite($fh,  $logText);
		fclose($fh);
	}
	
}