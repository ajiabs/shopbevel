<?php
	class Pixafy_Pixvote_ChallengesController extends Mage_Core_Controller_Front_Action 
	{
		//Upload image to temp folder is user is logged in
		function submitImageAction()
		{
			$json = array();
			$helper =  Mage::helper("pixvote");
				
			if(Mage::helper("customer")->isLoggedIn())
			{
				$customer = Mage::helper("customer")->getCustomer();
				$folderName = $helper->ENTRY_TEMP_IMAGE_FOLDER.$customer->getId();
				$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$folderName;
				try
				{
					/**
					 * Check to see if ENTRY_TEMP_IMAGE_FOLDER folder exists and if not, create it
					 */
					if (!is_dir($media_folder))
					{
						mkdir($media_folder, 0775, true);
					}

					/**
					 * Iterate through the $_FILES array and check each file
					 * If no errors exist then upload them to our directory
					 */
					$input_name = "entry-images";
					for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
					{	
						$fileInfo = $helper->uploadImage($i,$input_name, $media_folder);
						if($fileInfo)
						{
							extract($fileInfo);
							$getKey = 'get'.$helper->TEMP_IMAGE_SESSION_KEY;
							$setKey = 'set'.$helper->TEMP_IMAGE_SESSION_KEY;
							$unsetKey = 'uns'.$helper->TEMP_IMAGE_SESSION_KEY;
							$tempImageArr = unserialize(Mage::getSingleton('core/session')->$getKey());
							if(!$tempImageArr)
							{
								$tempImageArr = array();
							}
							$id = $this->getRequest()->getParam("id");
							$image = new Varien_Object();
							$image->setUrl($media_folder.DS.$server_name);
							$tempImageArr[$id] = $image->getUrl();
							Mage::getSingleton('core/session')->$setKey(serialize($tempImageArr));
							$json['id'] = $id;
							$json['image'] = $helper->getResizedImage($image, 'getUrl', 268,  null, $folderName);
						}
						else
						{
							$validImages = implode(",",$helper->VALID_IMAGE_TYPES);
							$json['error'] = $this->__("File unable to be uploaded. Please make sure your image is an accepted file type({$validImages})");
						}
					}
				}

				catch(Exception $e)
				{
					Mage::getSingleton('core/session')->addError($e->getMessage());
				}
			}
			else
			{
				$json['error'] = $helper->ERROR_LOGIN;
			}
			
			//echo result
			echo json_encode($json);
		}
		
		function submitAction()
		{
			
			$json = array();
			$entryData = $this->getRequest()->getParam("entry");
			$getKey = 'get'.Mage::helper("pixvote")->TEMP_IMAGE_SESSION_KEY;
			$pixvoteHelper = Mage::helper("pixvote");
			if(Mage::helper("customer")->isLoggedIn())	
			{	
				$hasImages = Mage::getSingleton('core/session')->$getKey();
				if($entryData && ($hasImages || isset($entryData['will_email_images'])))
				{
					$customer = Mage::helper("customer")->getCustomer();
					$entryData['customer_id'] = $customer->getId();
					$entryData['status_id'] = Mage::helper("pixvote")->ENTRY_STATUS_IN_PENDING;
					$entry = Mage::getModel(Mage::helper("pixvote")->DESIGNS_MODEL_PATH)->setData($entryData);
					if($entry->save())
					{
						if($hasImages)
						{
							Mage::dispatchEvent('pixafy_pixvote_entry_saved', array('entry' => $entry, 'use_session_images' => true));
						}
						//send submission email
						$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($entryData['challenge_id']);
						$emailHelper = Mage::helper("bevel/email");
						$vars = array(
								"design_challenge" => $challenge->getName(),
								"challenge_url" => $pixvoteHelper->getChallengeAboutUrl($challenge)
						);
						
						$upcomingChallenges = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->getCollection()
						->addFieldToFilter("appear_in_submission_email",$pixvoteHelper->APPEAR_IN_SUBMISSION_EMAIL)
						->setOrder("vote_start_time","ASC");
						$challengeCount = 1;
						foreach($upcomingChallenges as $upcomingChallenge)
						{
							$vars["challenge_image_{$challengeCount}"] = $pixvoteHelper->getResizedImage($upcomingChallenge, 'getImage', $pixvoteHelper->SUBMISSION_EMAIL_IMAGE_WIDTH, $pixvoteHelper->SUBMISSION_EMAIL_IMAGE_WIDTH);
							$vars["challenge_name_{$challengeCount}"] = $upcomingChallenge->getName();
							$vars["challenge_url_{$challengeCount}"] = $pixvoteHelper->getChallengeAboutUrl($upcomingChallenge);
							$challengeCount++;
						}
						$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_SUBMISSION, $customer->getEmail(), $vars);
						
						//Check if they are a designer and update if not
						if(!$customer->getIsDesigner())
						{
							$customer->setIsDesigner($pixvoteHelper->VAL_TRUE)->save();
						}
						
						//return profile url
						$json["redirect_url"] = $pixvoteHelper->getProfileUrl($customer);
					}
				}
				else
				{
					$json["error"] = Mage::helper("pixvote")->ERROR_UPLOAD_IMAGE;
				}
			}
			else
			{
				$json['error'] = Mage::helper("pixvote")->ERROR_LOGIN;
			}
			echo json_encode($json);
		}
	}
?>