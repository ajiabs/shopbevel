<?php
	class Pixafy_Pixvote_Adminhtml_DesignersController extends Mage_Adminhtml_Controller_Action
	{
		const CHALLENGES_MODEL_PATH = "pixvote/challenges";
		function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenuedesigners/view_designers');
			$this->renderLayout();
		}
		
		function newAction()
		{
			$this->_forward('edit');
		}
		
		function editAction()
		{
			$id = $this->getRequest()->getParam("id");
			$designer = mage::getModel('customer/customer')->load($id);
			if(is_object($designer))
			{
				Mage::register('designer_data', $designer);
				$this->loadLayout();
				$this->_setActiveMenu('pixvotemenuedesigners/edit_designer');
//				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry Manager'), Mage::helper('adminhtml')->__('Entry Manager'));
//				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry News'), Mage::helper('adminhtml')->__('Entry information'));
				$this->renderLayout();
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pixvote')->__('Record not found'));
				$this->_redirect('*/*/');
			}
		}
		
		function saveAction()
		{
			$designerData = $this->getRequest()->getParam("designer");
			$pixvoteHelper = Mage::helper("pixvote");
			if($designerData)
			{
				try
				{
					$designer = mage::getModel('customer/customer')->load($designerData['entity_id']);

					//if working with a new record, unset id 
					if(!$designer->getId())
					{
						unset($designerData['id']);
					}
					$input_name = "profile-picture";
					$designer->addData($designerData);
					if($designer->save())
					{
						$designer->setEntityId($designer->getId());
						Mage::dispatchEvent('pixafy_pixvote_designer_saved', array('entry' => $designer));
						if(count($_FILES[$input_name]['name']))
						{
							//Upload image if it was passed in
							$folderName = $pixvoteHelper->CUSTOMER_IMAGE_FOLDER.$designer->getId();
							$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$folderName;
							// Check to see if CUSTOMER_IMAGE_FOLDER folder exists and if not, create it
							if (!is_dir($media_folder))
							{
								mkdir($media_folder, 0775, true);
							}
							for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
							{	
								if(!$_FILES[$input_name]['name'][$i])
								{
									continue;
								}
								$fileInfo = $pixvoteHelper->uploadImage($i,$input_name, $media_folder);
								if($fileInfo)
								{
									extract($fileInfo);

									$oldImage = $designer->getProfileImage();

									$designer->setProfileImage($folderName.DS.$server_name);
									$designer->save();

									//Delete old imags if they exists
									if($oldImage)
									{
										unlink(Mage::getBaseDir('media').$oldImage);
										$pixvoteHelper->deleteResizedImages(Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.$folderName.DS.'resized'.DS.'*');
									}
									$json['image'] = $pixvoteHelper->getProfileImage($designer, true);
								}
								else
								{
									$validImages = implode(",",$pixvoteHelper->VALID_IMAGE_TYPES);
									Mage::getSingleton('adminhtml/session')->addError($this->__("File unable to be uploaded. Please make sure your image is an accepted file type({$validImages})"));
								}
							}
							Mage::dispatchEvent('pixafy_pixvote_designer_saveProfileImage', array('entry' => $designer));
						}
						
						$action = isset($designerData['entity_id']) ? "updated" : "created";
						$message = "designer {$action}";
						$method = "addSuccess";
						Mage::getSingleton('adminhtml/session')->setDesignerData(false);
					}
					else
					{
						$message = "An error occured while editing the designer";
						$method = "addError";
					}
					Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
					if ($this->getRequest()->getParam('back')) 
					{
						$this->_redirect('*/*/edit', array('id' => $designer->getId()));
					} else 
					{
						$this->_redirect('*/*/');
					}
					return;

				}
				catch(Exception $e)
				{
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setChallengeData($this->getRequest()->getPost());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
		}

		function deleteAction()
		{
			if($this->getRequest()->getParam('id') > 0)
			{
				try
				{
					$challengeModel = Mage::getModel(self::CHALLENGES_MODEL_PATH);

					$challengeModel->setId($this->getRequest()->getParam('id'))
						->delete();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('designer was successfully deleted'));
					$this->_redirect('*/*/');
				}
				catch (Exception $e)
				{
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
			}

			$this->_redirect('*/*/');
		}

		function gridAction()
		{
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('pixvote/adminhtml_designers_grid')->toHtml()
			);
		}
		
		function exportStatsAction()
		{
			$designerIds = $this->getRequest()->getParam("designers");
			$pixvoteHelper = Mage::helper("pixvote");
			//Prepare data if designers are passed in
			if($designerIds)
			{
				//build header
				$content = "";
				$header = array("Id", "Name", "Email", "City");
				//$csv = array(array("ID", "Name", "Email", "City"));
				$challenges = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->getCollection()->getItems();
				foreach($challenges as $challenge)
				{
//					$csv[0][] = $challenge->getName();
//					$csv[0][] = $challenge->getName().' Votes';
//					$csv[0][] = $challenge->getName().' Comments';
					$header[] = $challenge->getName();
					$header[] = $challenge->getName().' Votes';
					$header[] = $challenge->getName().' Comments';
				}
				$content .= implode(",", $header)."\n";
				$designers = Mage::getResourceModel("customer/customer_collection")->addAttributeToFilter("entity_id", $designerIds)
				->addAttributeToSelect(array("designer_city","designer_state","designer_country"))
				->addNameToSelect();
				$designerStats = array();
				foreach($designers as $designer)
				{
					$data = array();
					$data[] = '"'.$designer->getId().'"';
					$data[] = $designer->getName();
					$data[] = $designer->getEmail();
					$data[] = '"'.$designer->getLocation().'"';
					//Get designs; and order by challenge
					$designs = Mage::getModel($pixvoteHelper->DESIGNS_MODEL_PATH)->getCollection()
					->addFieldToSelect(array("id","challenge_id"))
					->addFieldToFilter("customer_id", $designer->getId())
					->addFieldToFilter("status_id", array("nin" => array($helper->ENTRY_STATUS_IN_REJECTED, $helper->ENTRY_STATUS_IN_PENDING)));
					$challengeStats = array();
					foreach($designs as $design)
					{
						if(!isset($challengeStats[$design->getChallengeId()]))
						{
							$challengeStats[$design->getChallengeId()] = array($design->getId());
						}
						else
						{
							$challengeStats[$design->getChallengeId()][] = $design->getId();
						}
						
						//load challenge stats
					}
					
					foreach($challenges as $challenge)
					{
						$data[] = '';
						$data[] = isset($challengeStats[$challenge->getId()]) ? Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", array("in" => $challengeStats[$challenge->getId()]))->count() : '';
						$data[] = isset($challengeStats[$challenge->getId()]) ? Mage::getModel($pixvoteHelper->DESIGN_COMMENTS_MODEL_PATH)->getCollection()->addFieldToFilter("design_id", array("in" => $challengeStats[$challenge->getId()]))->count() : ''; 
					}
					//$csv[] = $data;
					$content .= implode(",", $data)."\n";
				}
				 
				 
				 //Prepare for download
				 $mageCsv = new Varien_File_Csv();
				 $basePath = Mage::getBaseDir('export');
				 if (!is_dir($basePath))
				 {
					mkdir($basePath);
				 }	
				$fileName = "Designer_Stats.csv";
				$file_path = $basePath."/{$fileNname}";
				$this->_prepareDownloadResponse($fileName, $content);
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('No designers have been selected'));
			}
			//$this->_redirect('*/*/');
		}
		
		function exportBevelStatsAction()
		{
			$customerIds = $this->getRequest()->getParam("customer");
			if($customerIds)
			{
				//Get all customers in the DB
				$pixvoteHelper = Mage::helper("pixvote");
				$customers = Mage::getResourceModel("customer/customer_collection")
				->addAttributeToFilter('entity_id', $customerIds)
				->addAttributeToSelect("is_designer")
				->addNameToSelect()
				->getItems();
				//->addAttributeToSelect(array("designer_city","designer_state","designer_country")
				$votes = array();
				$orders = array();
				$content = "";
				$header = array("User ID", "email", "Is Designer", "Login/Signup Date", "Vote SKU #", "Voted Date", "Shop SKU #", "$", "Purchase Date", "Status");
				$content .= implode(",", $header)."\n";
				$now = Mage::getModel('core/date')->timestamp(time());
				//$customerInfo = array();
				//Load vote and shop date
				$dateFormat = "n/j/Y H:i:s";
				foreach($customers as $customer)
				{
					$createdAt = $customer->getCreatedAt();
					$createdAtTime = strtotime($createdAt, $now);
					$data = array();
					$data[] = '"'.$customer->getId().'"';
					$data[] = $customer->getEmail();
					$data[] = $customer->getIsDesigner() ? "YES" : "NO";
					$data[] = date($dateFormat, $createdAtTime);
					$votes[$customer->getId()] = Mage::getModel($pixvoteHelper->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter('customer_id', $customer->getId());
					$orders[$customer->getId()] = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $customer->getId());
					$content .= implode(",", $data)."\n";
				}
				
				foreach($votes as $customerId => $customerVotes)
				{
					$customer = $customers[$customerId];
					$data = array();
					$data[] = '"'.$customer->getId().'"';
					$data[] = $customer->getEmail();
					$data[] = $customer->getIsDesigner() ? "YES" : "NO";
					$data[] = '';
					foreach($customerVotes as $vote)
					{
						
						$voteData = $data;
						$createdAt = $vote->getCreatedOn();
						$createdAtTime = strtotime($createdAt);
						$voteData[] = $vote->getId();
						$voteData[] = date($dateFormat, $createdAtTime);
						$content .= implode(",", $voteData)."\n";
						if($vote->getId() == 454)
						{
							//var_dump($voteData);exit;
						}
					}
				}
				
				foreach($orders as $customerId => $customerOrders)
				{
					$customer = $customers[$customerId];
					$data = array();
					$data[] = '"'.$customer->getId().'"';
					$data[] = $customer->getEmail();
					$data[] = $customer->getIsDesigner() ? "YES" : "NO";
					$data[] = '';
					foreach($customerOrders as $order)
					{
						$orderData = $data;
						$createdAt = $order->getCreatedAt();
						$createdAtTime = strtotime($createdAt, $now);
						$orderData[] = '';
						$orderData[] = '';
						$orderData[] = $order->getIncrementId();
						$orderData[] = $order->getGrandTotal();
						$orderData[] = date($dateFormat, $createdAtTime);
						$orderData[] = $order->getStatus();
						$content .= implode(",", $orderData)."\n";
					}
				}

				// Create the CSV
				$time = Mage::getSingleton('core/date')->timestamp(time()-86400);
				$fileName = "shopbevel-{$time}.csv";
				$this->_prepareDownloadResponse($fileName, $content);
			}
		}
		
		function sendProfileRemainderAction()
		{
			$designerIds = $this->getRequest()->getParam("designers");
			$pixvoteHelper = Mage::helper("pixvote");
			$emailHelper = Mage::helper("bevel/email");

			//Send emails if ids were passed in
			if($designerIds)
			{
				$designers = Mage::getResourceModel("customer/customer_collection")->addAttributeToFilter("entity_id", $designerIds);
				foreach($designers as $designer)
				{
					$vars = array(
						"profile_url" => $pixvoteHelper->getProfileUrl($designer)
					);
					$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_PROFILE_NOT_DONE, $designer->getEmail(), $vars);
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Emails have been sent'));
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error has occured while sending the email'));
			}
			$this->_redirect('*/*/');
		}
		
		protected function _createRewriteUrl($designer, $idPath, $targetPath, $requestPath)
		{
//			$name = strtolower(str_replace(" ",Mage::helper("pixvote")->designer_SEPARATOR, $designer->getName()));
//			$idPath = "challenges/".$designer->getId();
//			$targetPath = "challenges/view/index/id/".$name;
//			$requestPath = 'challenges/'.$name;
			$model = Mage::getModel('core/url_rewrite')->loadByIdPath($idPath);
			Mage::helper('core/url_rewrite')->validateRequestPath($requestPath);

			$model->setIdPath($idPath)
			->setTargetPath($targetPath)
			->setOptions('')
			->setDescription('')
			->setRequestPath($requestPath);

			if (!$model->getId()) {
				$model->setIsSystem(0);
			}
			if (!$model->getIsSystem()) {
				$model->setStoreId($this->getRequest()->getParam('store_id', 0));
			}
			$model->save();
		}
	
	}
?>