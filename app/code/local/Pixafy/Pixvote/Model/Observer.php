<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixvote_Model_Observer extends Varien_Object
{
	function saveEntryImages($observer)
	{
		/*$s3Helper = Mage::helper("pixvote/s3");
		$entry = $observer->getEntry();
		exit;
		*/
		
		$helper =  Mage::helper("pixvote");
		$entry = $observer->getEntry();
		$useSessionImages = $observer->getUseSessionImages();
		$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$helper->ENTRY_IMAGE_FOLDER.$entry->getId();
		/**
		 * Check to see if media/entry_{entry_id} folder exists and if not, create it
		 */
		if (!is_dir($media_folder))
		{
			mkdir($media_folder, 0775, true);
		}
			
		if($useSessionImages)
		{
			$getKey = 'get'.$helper->TEMP_IMAGE_SESSION_KEY;
			$unsetKey = 'uns'.$helper->TEMP_IMAGE_SESSION_KEY;
			$tempImageArr = unserialize(Mage::getSingleton('core/session')->$getKey());
			foreach($tempImageArr as $key => $tempImage)
			{
				//Move temp image to entry folder
				$fileName = pathinfo($tempImage,PATHINFO_BASENAME);
				$filePath = $media_folder.DS.$fileName;
				copy($tempImage,$filePath);
				$entryImage = Mage::getModel($helper->DESIGN_IMAGES_MODEL_PATH);
				$entryImage->setDesignId($entry->getId());
				$entryImage->setUrl($helper->ENTRY_IMAGE_FOLDER.$entry->getId().DS.$fileName);
				$entryImage->setSortOrder($key);
				$entryImage->save();
				
				//Delete temp file
				unlink($tempImage);
			}
			
			//Clear session data
			Mage::getSingleton('core/session')->$unsetKey();
		}
		else
		{
			try
			{
				/**
				 * Iterate through the $_FILES array and check each file
				 * If no errors exist then upload them to our directory
				 */
				$input_name = "entry-image";
				for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
				{
					$fileInfo = $helper->uploadImage($i,$input_name, $media_folder);
					if($fileInfo)
					{
						//Store image info
						extract($fileInfo);
						$entryImage = Mage::getModel($helper->DESIGN_IMAGES_MODEL_PATH);
						$entryImage->setDesignId($entry->getId());
						$entryImage->setUrl($helper->ENTRY_IMAGE_FOLDER.$entry->getId().DS.$server_name);
						$entryImage->save();
					}
				}
			}

			catch(Exception $e)
			{
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}
	}
	
	function updateCategory($observer)
	{
		$designer = $observer->getEntry();				
		$category = Mage::getModel('catalog/category')->loadByAttribute('designer_id', $designer->getEntityId());
		
		if(!$designer) return;
		if($designer->getFirstname() && $designer->getLastname() && $category)
		{
			$category->setName($designer->getFirstname().' '.$designer->getLastname());
			$category->setDesigner($designer->getFirstname().' '.$designer->getLastname());
			$category->setDescription($designer->getDescription());
		}
		
		if($designer->getLocation() && $category)
		{
			$category->setLocation($designer->getLocation());
		}
		if($category)
		{
			$category->save();	
		}
	}
	
	function updateCategoryThumbnail($observer)
	{
		$designer = $observer->getEntry();				
		$designerCategory = Mage::getModel('catalog/category')->loadByAttribute('designer_id', $designer->getEntityId());
		
		if($designerCategory)
		{
			if($designer->getProfileImage())
			{
				$customerImagePath = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$designer->getProfileImage();
				$fileName = pathinfo($customerImagePath,PATHINFO_BASENAME);
				$categoryImagePath = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.'catalog'.DS.'category'.DS.$fileName;
				copy($customerImagePath,$categoryImagePath);
				$designerCategory->setThumbnail($fileName);			
			}
			$designerCategory->save();
		}
		
	}
	
	
	function deleteEntryImages($observer)
	{
		$imageIds = $observer->getImagesToRemove();
		if($imageIds)
		{
			$helper =  Mage::helper("pixvote");
			$entry = $observer->getEntry();
			$images = Mage::getModel($helper->DESIGN_IMAGES_MODEL_PATH)->getCollection()
			->addFieldToFilter('design_id', $entry->getId())
			->addFieldToFilter('id', array("in" => $imageIds));
			
			//Delete images
			foreach($images as $image)
			{
				 if(unlink(Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$image->getUrl()));
				 {
					//Delete record
					$image->delete();
				 }
			}
		}
	}
	
	function sendPrepareToWinEmail()
	{
		$logPath=Mage::getBaseDir('log').DS.'prepare_to_win_emails.log';
		$emailHelper = Mage::helper("bevel/email");
		$pixvoteHelper = Mage::helper("pixvote");
		$now = $pixvoteHelper->now();
		$nowSecs = strtotime($now);
		$nowDateTime = new DateTime($now);
		$dateFormat = 'Y-m-d  H:i:s';
		$dayInSecs = 86400;
		$lastWeekDateTime = clone $nowDateTime;
		$lastWeekDateTime->modify('-7 day');
		$PREPARE_EMAIL_SENT = 1;
		$PREPARE_EMAIL_NOT_SENT = 0;
		//Begin cron log
		$logText = "------Start Cron------\n\n";
		$logText .= "Crontime: ".$now."\n-------------\n";
		
		//Find Challenges that has ended submission in the last week
		$challengeCollection = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->getCollection()
		->addFieldToFilter("is_visible", $pixvoteHelper->IS_VISIBLE)
		->addFieldToFilter('submission_end_time', array('datetime' => true, 'from' => $lastWeekDateTime->format($dateFormat)))
		->addFieldToFilter('submission_end_time', array('datetime' => true, 'to' => $nowDateTime->format($dateFormat)))
		->addFieldToFilter("prepare_email_sent", $PREPARE_EMAIL_NOT_SENT);
		
		$challenges = array();
		$challengeDesigns = array();
		$challengeEmailsSent = array();
		$designers = array();
		
		//Find any challenge that is starting in 24 hours
		foreach($challengeCollection as $challenge)
		{
			$timeDiff = strtotime($challenge->getVoteStartTime()) - $nowSecs;
			//Check if registered for a day or more and that they have not receved the how it works email yet
			if($timeDiff <= $dayInSecs && !$challenge->getPrepareEmailSent())
			{
				$challenges[$challenge->getId()] = $challenge;
				$challengeDesigns[$challenge->getId()] = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
				->addFieldToFilter("status_id", array("in" => array($pixvoteHelper->ENTRY_STATUS_IN_VOTING, $pixvoteHelper->ENTRY_STATUS_IN_APPROVED)))
				->addFieldToFilter("challenge_id", $challenge->getId());
				//$challenge->setPrepareEmailSent(true);
			}
			
			//Send out emails
			foreach($challengeDesigns as $challengeId => $challengeDesign)
			{
				$logText .="Sending Prepare to win email for challenge ".$challenges[$challengeId]->getName()."; Challenge Vote Start Time:".$challenges[$challengeId]->getVoteStartTime()." \n-------------\n";
				$vars = array("challenge_vote_page" => $pixvoteHelper->getChallengeAboutUrl($challenges[$challengeId]));
				$challengeEmailSent[$challengeId] = array();
				
				foreach($challengeDesign as $design)
				{
					//check to see if email for this challenge wasn't already sent to designer
					if(!isset($challengeEmailsSent[$design->getCustomerId()]))
					{	if(!isset($designers[$design->getCustomerId()]))
						{
							$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
						}
						$vars['entry_url'] = Mage::getBaseUrl().$pixvoteHelper->getDesignUrl($design,$challenges[$challengeId]);
						$designer = $designers[$design->getCustomerId()];
						$logText .="Sending designer ".$designer->getId()."-".$designer->getName()." prepare to win email for challenge ".$challenges[$challengeId]->getName()."\n-------------\n";
//						if($design->getCustomerId() == 32)
//						{
							$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_PREPARE_TO_WIN, $designer->getEmail(), $vars);
//						}
						$challengeEmailsSent[$design->getCustomerId()] = $design->getCustomerId();
					}
				}
				$challenges[$challengeId]->setPrepareEmailSent($PREPARE_EMAIL_SENT)->save();
			}
		}
		$logText .= "------End cron------\n\n";
		$fh = fopen($logPath, 'a') ;
		fwrite($fh,  $logText);
		fclose($fh);
	}
	
	function sendInSessionEmail()
	{
		$logPath=Mage::getBaseDir('log').DS.'vote_in_session_emails.log';
		$emailHelper = Mage::helper("bevel/email");
		$pixvoteHelper = Mage::helper("pixvote");
		$bevelHelper =  Mage::helper("bevel");
		$now = $pixvoteHelper->now();
		$nowSecs = strtotime($now);
		$nowDateTime = new DateTime($now);
		$dateFormat = 'Y-m-d  H:i:s';
		$dayInSecs = 86400;
		$IN_SESSION_EMAIL_SENT = 1;
		$IN_SESSION_EMAIL_NOT_SENT = 0;
		//Begin cron log
		$logText = "------Start Cron------\n\n";
		$logText .= "Crontime: ".$now."\n-------------\n";
		
		//Find Challenges that has ended submission in the last week
		$challengeCollection = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->getCollection()
		->addFieldToFilter("is_visible", $pixvoteHelper->IS_VISIBLE)
		->addFieldToFilter('vote_start_time', array('datetime' => true, 'to' => $now))
		->addFieldToFilter("in_session_email_sent", $IN_SESSION_EMAIL_NOT_SENT);
		
		$challenges = array();
		$challengeDesigns = array();
		$challengeEmailsSent = array();
		$designers = array();
		
		//Find any challenge that is starting in 24 hours
		foreach($challengeCollection as $challenge)
		{
			$timeDiff = strtotime($challenge->getVoteStartTime()) - $nowSecs;
			//Check if registered for a day or more and that they have not receved the how it works email yet
			if($timeDiff <= $dayInSecs && !$challenge->getInSessionEmailSent())
			{
				$challenges[$challenge->getId()] = $challenge;
				$challengeDesigns[$challenge->getId()] = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
				->addFieldToFilter("status_id", array("in" => array($pixvoteHelper->ENTRY_STATUS_IN_VOTING, $pixvoteHelper->ENTRY_STATUS_IN_APPROVED)))
				->addFieldToFilter("challenge_id", $challenge->getId());
				//$challenge->setPrepareEmailSent(true);
			}
		}
			
		//Send out emails
		foreach($challengeDesigns as $challengeId => $challengeDesign)
		{
			$logText .="Sending in session to win email for challenge ".$challenges[$challengeId]->getName()."; Challenge Vote Start Time:".$challenges[$challengeId]->getVoteStartTime()." \n-------------\n";
			$challengeEmailSent[$challengeId] = array();
			$endTime= new DateTime($challenge->getVoteEndTime());
			$timeLeft = $nowDateTime->diff($endTime);
			$days = $timeLeft->format("%d");
			$hours = $timeLeft->format("%h");
			$minutes = $timeLeft->format("%i");
			$vars = array();
			$vars['time_left'] = $days.' '.$bevelHelper->pluralize($days, "day", "days").' '.$hours.' '.$bevelHelper->pluralize($hours, "hour", "hours").' '.$minutes.' '.$bevelHelper->pluralize($minutes, "minute", "minutes");

			foreach($challengeDesign as $design)
			{
				//check to see if email for this challenge wasn't already sent to designer
				if(!isset($challengeEmailsSent[$design->getCustomerId()]))
				{
					if(!isset($designers[$design->getCustomerId()]))
					{
						$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
					}
					$designer = $designers[$design->getCustomerId()];

					//Load Data for email
					$image = Mage::getModel($pixvoteHelper->DESIGN_IMAGES_MODEL_PATH)->getCollection()
					->setPageSize(1)
					->addFilter("design_id", $design->getId())
					->getFirstItem();

					//Populate vars
					$vars['designer_image'] =  $pixvoteHelper->getProfileImage($designer);
					if(!$vars['designer_image'])
					{
						$designer->setProfileImage("");
						$vars['designer_image'] =  $pixvoteHelper->getProfileImage($designer);
					}
					$vars['designer_name'] = $designer->getName();
					$vars['entry_url'] = Mage::getBaseUrl().$pixvoteHelper->getDesignUrl($design,$challenges[$challengeId]);
					$vars['designer_location'] =  $designer->getLocation();
					$vars['design_price'] =  $design->getBevelPrice();
					$vars['design_image'] =  $pixvoteHelper->getResizedImage($image, 'getUrl', $pixvoteHelper->DESIGN_IMAGE_WIDTH,  null);
					$vars['design_comment_count'] = Mage::getModel($pixvoteHelper->DESIGN_COMMENTS_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", $design->getId())->count();
					$vars['design_vote_count'] = Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", $design->getId())->count();
					
					//Send Email
//					if($design->getCustomerId() == 32)
//					{
						$logText .="Sending designer ".$designer->getId()."-".$designer->getName()." in session for challenge ".$challenges[$challengeId]->getName()."\n-------------\n";
						$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_VOTING_IN_SESSION, $designer->getEmail(), $vars);
//					}
					$challengeEmailsSent[$design->getCustomerId()] = $design->getCustomerId();
				}
			}
			$challenges[$challengeId]->setInSessionEmailSent($IN_SESSION_EMAIL_SENT)->save();
		}
		$logText .= "------End cron------\n\n";
		$fh = fopen($logPath, 'a') ;
		fwrite($fh,  $logText);
		fclose($fh);
	}
	
	//Find all designers that had their designs approved in the last 24hrs and
	//if the profile is not completed, send email
	function sendProfileNotDoneEmail()
	{
		$logPath=Mage::getBaseDir('log').DS.'profile_not_done_emails.log';
		$dayInSecs = 86400;
		$emailHelper = Mage::helper("bevel/email");
		$pixvoteHelper = Mage::helper("pixvote");
		$now = Mage::getModel('core/date')->timestamp(time());
		$nowSecs = strtotime($now);
		$dateFormat = 'Y-m-d H:i:s';
		$today = date($dateFormat, $now);
		$todayTime = strtotime($today);
		$todayDateTime = new DateTime($today);
		$lastWeekDateTime = clone $todayDateTime;
		$lastWeekDateTime->modify('-7 day');
		
		//vars
		$designers = array();
		$emailsSent = array();
		
		//Begin cron log
		$logText = "------Start Cron------\n\n";
		$logText .= "Crontime: ".$today."\n-------------\n";
		
		//find designs approved in the last week
		$designs = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
		->addFieldToFilter('status_id', $pixvoteHelper->ENTRY_STATUS_IN_APPROVED)
		->addFieldToFilter('approval_time', array('datetime' => true, 'from' => $lastWeekDateTime->format($dateFormat)))
		->addFieldToFilter('approval_time', array('datetime' => true, 'to' => $todayDateTime->format($dateFormat)))
		->addFieldToFilter('profile_not_complete_email_sent', $pixvoteHelper->VAL_FALSE);
		$logText .= "Entries found being approved between" .$lastWeekDateTime->format($dateFormat)." and ".$todayDateTime->format($dateFormat).": ".count($designs)."\n-------------\n";
		
		foreach($designs as $design)
		{
			//Check if approved in the last 24 hrs 
			$timeDiff = strtotime($design->getApprovalTime()) - $nowSecs;
			if($timeDiff <= $dayInSecs)
			{
				//Get the designer
				if(!isset($emailsSent[$design->getCustomerId()]))
				{
					//Load designer; check if profile is completed
					if(!isset($designers[$design->getCustomerId()]))
					{
						$designers[$design->getCustomerId()] = Mage::getModel('customer/customer')->load($design->getCustomerId());
					}
					$designer = $designers[$design->getCustomerId()];
					
					if(!$designer->checkIfProfileIsCompleted())
					{
						//Send email
						$logText .= "Sending profile not completed email to ".$designer->getId()."-".$designer->getName()."\n-------------\n";
						$vars = array(
							"profile_url" => $pixvoteHelper->getProfileUrl($designer)
						);
						$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_PROFILE_NOT_DONE, $designer->getEmail(), $vars);
					}
					$emailsSent[$design->getCustomerId()] = $design->getCustomerId();
				}
				$design->setProfileNotCompleteEmailSent($pixvoteHelper->VAL_TRUE);
			}
		}
		$designs->save();
		
		//Save log
		$logText .= "------End cron------\n\n";
		$fh = fopen($logPath, 'a') ;
		fwrite($fh,  $logText);
		fclose($fh);
	}
	
	function sendDailyStatsEmail()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$emailHelper = Mage::helper("bevel/email");
		$shopBevelHelper = Mage::helper('bevel');
		$NO_CHANGE = $pixvoteHelper->__("No Change");
		$MODIFY_DAY_BEOFRE = '-1 day';
		$POPULAR_DESIGN_LIMIT= 3;
		$now = $pixvoteHelper->now();
		$nowDateTime = new DateTime($now);
		$yesterdayDateTime = clone $nowDateTime;
		$yesterdayDateTime->modify($MODIFY_DAY_BEOFRE);
		$dayBeforeYesterdayDateTime = clone $yesterdayDateTime;
		$dayBeforeYesterdayDateTime->modify($MODIFY_DAY_BEOFRE);
		$designStats = array();
		$designStatTotals = array();
		$topEntries = array();
		$count = 1;
		$vars  = array();
		
		//Begin log
		$logPath=Mage::getBaseDir('log').DS.'daily_stats_emails.log';
		$logText = "------Start Cron------\n\n";
		$logText .= "Crontime: ".$now."\n-------------\n";
		
		//Get current challenges
		$challenges = $pixvoteHelper->getAvailableChallenges()->getItems();
		
		//Get designs 
		$challengeFilter = array("in" => array_keys($challenges));
		$designs  = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
		->addFieldToFilter("status_id", $pixvoteHelper->ENTRY_STATUS_IN_VOTING)
		->addFieldToFilter("challenge_id", $challengeFilter)->getItems();
		foreach($designs as $design)
		{	
			if(!isset($designers[$design->getCustomerId()]))
			{
				$designers[$design->getCustomerId()] = Mage::getModel("customer/customer")->load($design->getCustomerId());
			}
			$designer = $designers[$design->getCustomerId()];
			
			//Get total stats, and stats from yesteray
			$totalStats = $pixvoteHelper->getDesignStats($design->getId(),'', '');
			$todaysStats = $pixvoteHelper->getDesignStats($design->getId(),$yesterdayDateTime->format($pixvoteHelper->TIMESTAMP_FORMAT), $now);
			
			//Check if votes,views, or counts were added
			$commentDelta = $todaysStats->getCommentCount();
			if($commentDelta > 0)
			{
				$commentDelta = "+ ".$commentDelta." ".$shopBevelHelper->pluralize($commentDelta, "comment", "comments");
			}
			else
			{
				$commentDelta = $NO_CHANGE;
			}
			
			$viewDelta = $todaysStats->getViewCount();
			if($viewDelta > 0)
			{
				$viewDelta = "+ ".$viewDelta." ".$shopBevelHelper->pluralize($viewDelta, "view", "views");
			}
			else
			{
				$viewDelta = $NO_CHANGE;
			}
			$voteDelta = $todaysStats->getVoteCount();
			if($voteDelta > 0)
			{
				$voteDelta = "+ ".$voteDelta." ".$shopBevelHelper->pluralize($voteDelta, "vote", "votes");
			}
			else
			{
				$voteDelta = $NO_CHANGE;
			}
			$totalStats->setCommentDelta($commentDelta);
			$totalStats->setViewDelta($viewDelta);
			$totalStats->setVoteDelta($voteDelta);
			$total = $todaysStats->getCommentCount() + $todaysStats->getViewCount() + $todaysStats->getVoteCount();
			$designStats[$design->getId()] = $totalStats;
			$designStatTotals[$design->getId()] = $total;
		}
		
		//Get first $POPULAR_DESIGN_LIMIT of stat totals
		arsort($designStatTotals);
		$mostPopular = array_keys(current(array_chunk($designStatTotals,$POPULAR_DESIGN_LIMIT, true)));
		$timeRemaining = array();
		foreach($mostPopular as $designId)
		{
			$design = $designs[$designId];
			$designer = $designers[$design->getCustomerId()];
			$image = Mage::getModel($pixvoteHelper->DESIGN_IMAGES_MODEL_PATH)->getCollection()
			->setPageSize(1)
			->addFilter("design_id", $design->getId())
			->getFirstItem();
			$vars['top_designer_image_'.$count] =  $pixvoteHelper->getProfileImage($designer);
			if(!$vars['top_designer_image_'.$count])
			{
				$designer->setProfileImage("");
				$vars['top_designer_image_'.$count] =  $pixvoteHelper->getProfileImage($designer);
			}
			
			//Populate top entries data
			$vars['top_designer_name_'.$count] = $designer->getName();
			$vars['top_entry_url_'.$count] = Mage::getBaseUrl().$pixvoteHelper->getDesignUrl($design,$challenges[$challengeId]);
			$vars['top_designer_location_'.$count] =  $designer->getLocation();
			$vars['top_design_price_'.$count] =  $design->getBevelPrice();
			$vars['top_design_image_'.$count] =  $pixvoteHelper->getResizedImage($image, 'getUrl', $pixvoteHelper->DESIGN_IMAGE_WIDTH,  null);
			$vars['top_design_comment_count_'.$count] = Mage::getModel($pixvoteHelper->DESIGN_COMMENTS_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", $design->getId())->count();
			$vars['top_design_vote_count_'.$count] = Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", $design->getId())->count();
			if(!isset($timeRemaining[$design->getChallengeId()]))
			{
				$challenge = $challenges[$design->getChallengeId()];
				$endTime= new DateTime($challenge->getVoteEndTime());
				$timeLeft = $nowDateTime->diff($endTime);
				$days = $timeLeft->format("%d");
				$hours = $timeLeft->format("%h");
				$minutes = $timeLeft->format("%i");
				$timeRemaining[$challenge->getId()] = $days.' '.$shopBevelHelper->pluralize($days, "day", "days").' </br> '.$hours.' '.$shopBevelHelper->pluralize($hours, "hour", "hours").' </br> '.$minutes.' '.$shopBevelHelper->pluralize($minutes, "minute", "minutes");
			}
			$vars['time_remaining_'.$count]= $timeRemaining[$design->getChallengeId()];
			$count++;
		}
		
		//Get tip info
		$sendDate = current(explode(" ",$now));
		$tip = Mage::getModel($pixvoteHelper->DESIGN_TIPS_MODEL_PATH)->load($sendDate,'send_date');
		if(!$tip->getId())
		{
			//Load random tip
			$tipCollection = Mage::getModel($pixvoteHelper->DESIGN_TIPS_MODEL_PATH)->getCollection()->getItems();
			$tip = $tipCollection[array_rand($tipCollection)];
		}
		$vars['tip_text'] = $tip->getText(); 
		if(!isset($designers[$tip->getCustomerId()]))
		{
			$tipDesigner = Mage::getModel("customer/customer")->load($tip->getCustomerId());
		}
		else
		{
			$tipDesigner = $designers[$tip->getCustomerId()];
		}
		$vars['tip_text'] = $tip->getText(); 
		$vars['tip_name'] = $tipDesigner->getName();
		$vars['tip_image'] =  $pixvoteHelper->getResizedImage($tipDesigner, 'getProfileImage', $pixvoteHelper->TIP_EMAIL_IMAGE_WIDTH,  null);
		$vars['tip_location'] = $tipDesigner->getLocation(); 
		
		//Send out emails
		/*foreach($designStats as $designId => $stats)
		{
			$designer = $designers[$designs[$designId]->getCustomerId()];
			$logText .="Sending Daily stats email for design ".$designId."-".$designs[$designId]->getName()."\n";
			$logText .="Comments: ".$stats->getCommentCount()."; Comments Delta: ".$stats->getCommentDelta()." \n";
			$logText .="Votes: ".$stats->getVoteCount()."; Votes Delta: ".$stats->getVoteDelta()." \n";
			$logText .="Views: ".$stats->getViewCount()."; Views Delta: ".$stats->getViewDelta()." \n";
			$logText .="\n-------------\n";
			$vars['comment_count'] = $stats->getCommentCount();
			$vars['comment_delta'] = $stats->getCommentDelta();
			$vars['view_count'] =  $stats->getViewCount();
			$vars['view_delta'] = $stats->getViewDelta();
			$vars['vote_count'] = $stats->getVoteCount();
			$vars['vote_delta'] = $stats->getVoteDelta();
			//echo '<pre>'.print_r($vars, true).'</pre>';exit;
			//echo $designId;
			//echo $designer->getEmail().'</br>';
			$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_DAILY_STATS, $designer->getEmail(), $vars);
		}*/
		
		//echo '<pre>'.print_r($vars, true).'</pre>';
		$logText .= "------End ".$pixvoteHelper->now()." cron------\n\n";
		$fh = fopen($logPath, 'a') ;
		fwrite($fh,  $logText);
		fclose($fh);
	}
	
	function CheckForTracking($observer)
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$frontController = Mage::app()->getFrontController();
		$request = $frontController->getRequest();
		$session = $observer->getEvent()->getCustomerSession();
		if($session->isLoggedIn() && $request->getParam("track"))
		{
			$cookie = Mage::getModel('core/cookie');
			$trackingKey = $request->getParam("track");
			$id = Mage::getSingleton("core/encryption")->decrypt($cookie->get($trackingKey."_id"));
			if($id)
			{
					$pixvoteHelper = Mage::helper("pixvote");
					$trackingRec = Mage::getModel($pixvoteHelper->TRACKING_MODEL_PATH)->load($id);
					$trackingRec->setOutcome($pixvoteHelper->VAL_TRUE);
					$trackingRec->setUpdatedOn($pixvoteHelper->now());
					if($trackingRec->save())
					{
						$cookie->delete($trackingKey."_id");
						$cookie->delete($trackingKey."_subject");
					}
			}
		}
	}
}
?>
