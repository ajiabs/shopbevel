<?php
class Pixafy_Pixvote_DesignersController extends Mage_Core_Controller_Front_Action 
{
	function indexAction()
	{
		$id = $this->getRequest()->getParam("id");
		$designer = Mage::getModel("customer/customer")->load($id);
		$pixvoteHelper = Mage::helper("pixvote");
		if(!$designer->getId())
		{
			Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__("Designer not found"));
			return $this->_redirect('');
		}
		else
		{
			Mage::register($pixvoteHelper->CURRENT_DESIGNER_KEY, $designer);
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->renderLayout();
		}
	}

	function saveAction()
	{
		$designerData = $this->getRequest()->getParam("designer");
		$json = array();
		if($designerData)
		{
			$customer = Mage::helper("customer")->getCustomer();
			$customer->addData($designerData);
			if(!$customer->save())
			{
				$json['error'] = $this->__('Your information could not be saved');								
			}
			else
			{
				//dispatch the callback function in the observer
				Mage::dispatchEvent('pixafy_pixvote_designer_saved', array('entry' => $customer));
				$json['id'] = $this->getRequest()->getParam("id");
				$this->_checkIfComplete();
			}
		}
		else
		{
			$json['error'] = $this->__('No data has been passed');
		}
		echo json_encode($json);
	}
	function followAction()
	{
		$json = array();
		$pixvoteHelper = Mage::helper("pixvote");
		 if(Mage::helper("customer")->isLoggedIn())
		 {
			$customer = Mage::helper("customer")->getCustomer();
			$designerId = $this->getRequest()->getParam("id");
			$designer = Mage::getModel("customer/customer")->load($designerId);
			if($designer->getId())
			{
				//Check if record exits
				$json['id'] = $designer->getId();
				$existing = Mage::getModel($pixvoteHelper->DESIGN_FOLLOW_MODEL_PATH)->getCollection()
				->addFieldToFilter("customer_id", $customer->getId())
				->addFieldToFilter("designer_id", $designerId);
				if(!count($existing))
				{
					//Create record update count
					$data = array(
						"customer_id" => $customer->getId(),
						"designer_id" => $designerId
					);
					$record = Mage::getModel($pixvoteHelper->DESIGN_FOLLOW_MODEL_PATH)->setData($data);
					$record->save();
				}
				else
				{
					$json['error'] = $this->__('You are already following this designer');
				}
			}
			else
			{
				$json['error'] = $pixvoteHelper->ERROR_INVALID_DATA;
			}
		 }
		 else
		 {
			 $json['error'] = $pixvoteHelper->ERROR_LOGIN;
		 }
		 echo json_encode($json);
	}
	
	function saveProfileImageAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$json = array();
		if(Mage::helper("customer")->isLoggedIn())
		{
			$designer = Mage::helper("customer")->getCustomer();
			$designerCategory = Mage::getModel('catalog/category')->loadByAttribute('designer_id', $designer->getEntityId());
			$folderName = $pixvoteHelper->CUSTOMER_IMAGE_FOLDER.$designer->getId();
			$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$folderName;
			try
			{
				/**
				 * Check to see if CUSTOMER_IMAGE_FOLDER folder exists and if not, create it
				 */
				if (!is_dir($media_folder))
				{
					mkdir($media_folder, 0775, true);
				}

				/**
				 * Iterate through the $_FILES array and check each file
				 * If no errors exist then upload them to our directory
				 */
				$input_name = "profile-picture";
				for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
				{	
					$fileInfo = $pixvoteHelper->uploadImage($i,$input_name, $media_folder);
					if($fileInfo)
					{
						extract($fileInfo);
						
						$oldImage = $designer->getProfileImage();
						
						$designer->setProfileImage($folderName.DS.$server_name);
						$designer->save();
						
						//dispatch event callback function updateCategoryThumbnail
						Mage::dispatchEvent('pixafy_pixvote_designer_saveProfileImage', array('entry' => $designer));	
						
						//Delete old imags if they exists
						if($oldImage)
						{
							//echo Mage::getBaseDir('media').DS.$folderName.DS.'resized'.DS.'*';exit;
							unlink(Mage::getBaseDir('media').$oldImage);
							$pixvoteHelper->deleteResizedImages(Mage::getBaseDir('media').DS.$folderName.DS.'resized'.DS.'*');
						}
						$json['image'] = $pixvoteHelper->getProfileImage($designer, true);
						$this->_checkIfComplete();
					}
					else
					{
						$validImages = implode(",",$pixvoteHelper->VALID_IMAGE_TYPES);
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
			$json['error'] = $pixvoteHelper->ERROR_LOGIN;
		}
		echo json_encode($json);
	}
	
	function saveStudioImageAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$json = array();
		if(Mage::helper("customer")->isLoggedIn())
		{
			$studioId = $this->getRequest()->getParam("studio-id");
			$designer = Mage::helper("customer")->getCustomer();
			$folderName = $pixvoteHelper->CUSTOMER_STUDIO_IMAGE_FOLDER.$designer->getId();
			$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$folderName;
			try
			{
				/**
				 * Check to see if CUSTOMER_IMAGE_FOLDER folder exists and if not, create it
				 */
				if (!is_dir($media_folder))
				{
					mkdir($media_folder, 0775, true);
				}

				/**
				 * Iterate through the CUSTOMER_STUDIO_IMAGE_FOLDER array and check each file
				 * If no errors exist then upload them to our directory
				 */
				$input_name = "profile-studio";
				if(count($_FILES[$input_name]['name']))
				{
					for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
					{	
						$fileInfo = $pixvoteHelper->uploadImage($i,$input_name, $media_folder);
						if($fileInfo)
						{
							extract($fileInfo);


							//Check for existing image and do the neccessary actions
							$exitingstudioImage = Mage::getModel($pixvoteHelper->DESIGN_STUDIO_IMAGES_MODEL_PATH)->getCollection()
							->addFieldToFilter("id",$studioId)
							->addFieldToFilter("designer_id",$designer->getId());
							if(count($exitingstudioImage))
							{
								$studioImage = $exitingstudioImage->getFirstItem();
							}
							else
							{
								$studioImage = Mage::getModel($pixvoteHelper->DESIGN_STUDIO_IMAGES_MODEL_PATH);
							}

							//Get old image if any; set new info
							$oldImage = $studioImage->getUrl();
							$studioImage->setUrl($folderName.DS.$server_name);
							$studioImage->setDesignerId($designer->getId());
							$studioImage->save();

							//Delete old imags if they exists
							if($oldImage)
							{
								//echo Mage::getBaseDir('media').DS.$folderName.DS.'resized'.DS.'*';exit;
								unlink(Mage::getBaseDir('media').$oldImage);
								$pixvoteHelper->deleteResizedImages(Mage::getBaseDir('media').DS.$folderName.DS.'resized'.DS.'*');
							}

							//Prepare response data
							$json['image'] = $pixvoteHelper->getResizedImage($studioImage, 'getUrl', $pixvoteHelper->STUDIO_IMAGE_WIDTH,  $pixvoteHelper->STUDIO_IMAGE_HEIGHT);
							$json['id'] = $this->getRequest()->getParam("id");
							$json['studio-id'] = $studioImage->getId();
						}
						else
						{
							$validImages = implode(",",$pixvoteHelper->VALID_IMAGE_TYPES);
							$json['error'] = $this->__("File unable to be uploaded. Please make sure your image is an accepted file type({$validImages})");
						}
					}
				}
				else if($studioId)
				{
					$exitingstudioImage = Mage::getModel($pixvoteHelper->DESIGN_STUDIO_IMAGES_MODEL_PATH)->getCollection()
					->addFieldToFilter("id",$studioId)
					->addFieldToFilter("designer_id",$designer->getId());
					if(count($exitingstudioImage))
					{
						$exitingstudioImage->getFirstItem()->delete();
						$json['image'] = '';
						$json['id'] = $this->getRequest()->getParam("id");
						$json['studio-id'] = $studioId;
					}
				}
				else
				{
					//meh
				}
			}

			catch(Exception $e)
			{
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}
		else
		{
			$json['error'] = $pixvoteHelper->ERROR_LOGIN;
		}
		echo json_encode($json);
	}
	
	function regionsAction()
	{
		$countryId = $this->getRequest()->getParam("country");
		if($countryId)
		{
			$regions = Mage::getModel('directory/country')->load($countryId)->getRegions()->toArray();
			echo json_encode($regions);
		}	
	}
	
	//Check to see if we need to send PROFILE_COMPLETED email
	private function _checkIfComplete()
	{
		$customer = Mage::helper("customer")->getCustomer();
		if($customer->checkIfProfileIsCompleted() && !$customer->getProfileCompletedEmailSent())
		{
			//Send email
			$emailHelper = Mage::helper("bevel/email");
			$pixvoteHelper = Mage::helper("pixvote");
			
			//Get last design submission
			$latestEntry = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
			->addFieldToFilter("customer_id", $customer->getId())
			->addFieldToFilter("status_id", array("nin" => array($pixvoteHelper->ENTRY_STATUS_IN_REJECTED, $pixvoteHelper->ENTRY_STATUS_IN_PENDING)))
			->getLastItem();
			
			$vars = array(
				"profile_url" => $pixvoteHelper->getProfileUrl($customer)
			);
			
			if(is_object($latestEntry))
			{
				$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($latestEntry->getChallengeId());
				$vars['entry_url'] = Mage::getBaseUrl().$pixvoteHelper->getDesignUrl($latestEntry, $challenge);
			}
			else
			{
				$vars['entry_url'] = $pixvoteHelper->getProfileUrl($customer);
			}
	
			$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_PROFILE_DONE, $customer->getEmail(), $vars);
			//$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_WHILE_YOU_WAIT, $customer->getEmail());
			$customer->setProfileCompletedEmailSent(true)->save();
		}
	}
}
?>