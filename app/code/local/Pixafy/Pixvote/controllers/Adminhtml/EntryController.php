<?php
class Pixafy_Pixvote_Adminhtml_EntryController extends Mage_Adminhtml_Controller_Action
{
	const CONVERT_PRODUCT_ERROR = 'Error: Could not create product. please check if design is not already a product';
	function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('pixvotemenu/vote');
		$this->renderLayout();
	}
	
	function newAction()
	{
		$this->_forward('edit');
	}
	
	function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$entry = mage::getModel(Mage::helper('pixvote')->DESIGNS_MODEL_PATH)->load($id);
		if(is_object($entry))
		{
			//check if vote is set, if not, set to current count
			if(!$entry->getVotes())
			{
				$voteCnt = Mage::getModel(Mage::helper('pixvote')->DESIGN_VOTES_MODEL_PATH)->getCollection()->addFieldToFilter('design_id', $entry->getId())->count();
				$entry->setVotes($voteCnt)->save();
			}	
			Mage::register('entry_data', $entry);
			$entryImages = Mage::getModel(Mage::helper('pixvote')->DESIGN_IMAGES_MODEL_PATH)->getCollection()->addFieldToFilter('design_id', $entry->getId());
			Mage::register('entry_images', $entryImages);
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenu/entry');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry Manager'), Mage::helper('adminhtml')->__('Entry Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Entry News'), Mage::helper('adminhtml')->__('Entry information'));
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
		$entryData = $this->getRequest()->getParam("entry");
		$helper = Mage::helper("pixvote");
		$statusExceptions =  array(
			$helper->ENTRY_STATUS_IN_VOTING,
			$helper->ENTRY_STATUS_IN_PRODUCTION,
			$helper->ENTRY_STATUS_IN_EXPIRED,
			$helper->ENTRY_STATUS_IN_LIVE,
		);
		if($entryData)
		{
			try
			{
				$entry = mage::getModel("pixvote/designs")->load($entryData['id']);
				$oldStatus = $entry->getStatusId();
				if(in_array($entryData['status_id'], $statusExceptions) && $oldStatus  == $helper->ENTRY_STATUS_IN_PENDING)
				{
					$statusModel = Mage::getModel($helper->STATUS_MODEL_PATH)->load($entryData['status_id']);
					$error = "Error: Cannot change entry status from Pending to ".$statusModel->getValue()." before approving the entry";
					Mage::getSingleton('adminhtml/session')->addError($helper->__($error));
					$this->_redirect('*/*/edit', array('id' => $entry->getId()));
					return;
				}
				
				//if working with a new record, unset id 
				if(!$entry->getId())
				{
					unset($entryData['id']);
				}
				else
				{
					$entryData['updated_on'] = $helper->now();
					if($entryData['status_id'] == $helper->ENTRY_STATUS_IN_APPROVED && $oldStatus != $helper->ENTRY_STATUS_IN_APPROVED)
					{
						$entryData['approval_time'] = $entryData['updated_on'];
					}
				}
				$default = '0000-00-00 00:00:00';
				if(!$entryData['vote_start_time'])
				{
					unset($entryData['vote_start_time']);
				}
				
				if(!$entryData['vote_end_time'])
				{
					unset($entryData['vote_end_time']);
				}
				
				if($entryData['votes'] != $entry->getVotes() && $entry->getVotes())
				{
					//update vote count;
					$delta = $entryData['votes'] - $entry->getVotes();
					$voteCountModel = Mage::getModel("core/config_data")->load($helper->VOTE_COUNT_PATH,"path");
					$currentVoteCount = $voteCountModel->getValue();
					$newVoteCount = $currentVoteCount + $delta;
					$voteCountModel->setValue($newVoteCount)->save();
				}
				
				$entry->addData($entryData);
				if($entry->save())
				{
					Mage::dispatchEvent('pixafy_pixvote_entry_saved', array('entry' => $entry, 'images_to_remove' => $this->getRequest()->getParam("entry-image-delete")));
					$action = isset($entryData['id']) ? "updated" : "created";
					$message = "Entry {$action}";
					$method = "addSuccess";
					Mage::getSingleton('adminhtml/session')->setEntryData(false);
					if($entryData['status_id'] == $helper->ENTRY_STATUS_IN_APPROVED)
					{
						$this->_setEntryRewrite($entry);
						if($oldStatus != $helper->ENTRY_STATUS_IN_APPROVED)
						{
							//$entry->setApprovalTime($entryData['updated_on'])->save();
							$this->_sendApproval($entryData['customer_id']);
						}	
					}
					//else if($entryData['status_id'] == $helper->ENTRY_STATUS_IN_REJECTED)
					else if($entryData['status_id'] == $helper->ENTRY_STATUS_IN_REJECTED && $oldStatus != $helper->ENTRY_STATUS_IN_REJECTED)
					{
						$rejectionId = $this->getRequest()->getParam("rejection_reason");
						$this->_sendRejection($entryData['customer_id'], $rejectionId, $entryData['challenge_id']);
					}
					else if($entryData['status_id'] == $helper->ENTRY_STATUS_IN_PRODUCTION && $oldStatus != $helper->ENTRY_STATUS_IN_PRODUCTION)
					{
						//Convert to product
						$newProduct = $entry->_convertToProduct();
						if($newProduct)
						{
							$newProductMessage = 'Design '.$entry->getName().' has been converted to a product. view product <a target="_blank" href="'.$newProduct->getProductUrl().'">here</a>';
							Mage::getSingleton('adminhtml/session')->addSuccess($helper->__($newProductMessage));
						}
						else
						{
							$newProductMessage = self::CONVERT_PRODUCT_ERROR;
							Mage::getSingleton('adminhtml/session')->addError($helper->__($newProductMessage));
						}
					}
				}
				else
				{
					$message = "An error occured while creating the entry!";
					$method = "addError";
				}
				Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
				if ($this->getRequest()->getParam('back')) 
				{
					$this->_redirect('*/*/edit', array('id' => $entry->getId()));
				} else 
				{
					$this->_redirect('*/*/');
				}
                return;
				
			}
			catch(Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setEntryData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
	}
	
	public function deleteAction()
	{
		if($this->getRequest()->getParam('id') > 0)
		{
			try
			{
				$entryModel = Mage::getModel('pixvote/designs');

				$entryModel->setId($this->getRequest()->getParam('id'))
					->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Entry was successfully deleted'));
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
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('pixvote/adminhtml_entry_grid')->toHtml()
		);
	}
	
	function statusEditAction()
	{
		$entryIds = $this->getRequest()->getParam("entries");
		$newStatusId = $this->getRequest()->getParam("id");
		$helper = Mage::helper("pixvote");
		// Verify status is valid
		$newStatus = Mage::getModel($helper->STATUS_MODEL_PATH)->load($newStatusId);
		$oldStatuses = array();
		$now = $helper->now();
		//Update status of entries if valid
		if($entryIds && $newStatus->getid())
		{
			$entries = Mage::getModel($helper->DESIGNS_MODEL_PATH)->getCollection()->addFieldToFilter("id", $entryIds);
			foreach($entries as $entry)
			{
				$oldStatuses[$entry->getId()] = $entry->setStatusId();
				$entry->setStatusId($newStatusId);
				$entry->setUpdatedOn($now);
				if($newStatusId == $helper->ENTRY_STATUS_IN_APPROVED && $oldStatuses[$entry->getId()] != $helper->ENTRY_STATUS_IN_APPROVED)
				{
					$entry->setApprovalTime($now);
				}
				else if($newStatusId == $helper->ENTRY_STATUS_IN_PRODUCTION && $oldStatuses[$entry->getId()] != $helper->ENTRY_STATUS_IN_PRODUCTION)
				{
					//Convert to product
					$newProduct = $entry->_convertToProduct();
					if($newProduct)
					{
						$newProductMessage = 'Design '.$entry->getName().' has been converted to a product. view product <a target="_blank" href="'.$newProduct->getProductUrl().'">here</a>';
						Mage::getSingleton('adminhtml/session')->addSuccess($helper->__($newProductMessage));
					}
					else
					{
						$newProductMessage = self::CONVERT_PRODUCT_ERROR;
						Mage::getSingleton('adminhtml/session')->addError($helper->__($newProductMessage));
					}
				}
			}
			$entries->save();
			
			//Rewrites if new status is approved
			if($newStatusId == $helper->ENTRY_STATUS_IN_APPROVED)
			{
				foreach($entries as $entry)
				{
					$this->_setEntryRewrite($entry);
					if($oldStatuses[$entry->getId()] != $helper->ENTRY_STATUS_IN_APPROVED)
					{
						$this->_sendApproval($entry->getCustomerId());
					}
				}
			}
			//$entries->save();
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Entry status changed to '.$newStatus->getValue()));
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Required data was not passed'));
		}
		$this->_redirect('*/*/');
	}
	
	
	private function _setEntryRewrite($entry)
	{
		$helper = Mage::helper("pixvote");
		$challenge = Mage::getModel($helper->CHALLENGES_MODEL_PATH)->load($entry->getChallengeId());
		$name = strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $challenge->getName()));
		$idPath = "design/".$entry->getId();
		$targetPath = "challenges/index/view/id/".$entry->getId();
		$requestPath = $helper->getDesignUrl($entry,$challenge);
		$this->_createRewriteUrl($challenge, $idPath, $targetPath, $requestPath);
	}
	
	//TODO get
	protected function _createRewriteUrl($challenge, $idPath, $targetPath, $requestPath)
	{
//			$name = strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $challenge->getName()));
//			$idPath = "challenges/".$challenge->getId();
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
	
	function _sendApproval($customerId)
	{
		$customer = Mage::getModel("customer/customer")->load($customerId);
		$emailHelper = Mage::helper("bevel/email");
		$pixvoteHelper = Mage::helper("pixvote");
		$vars = array(
			"profile_url" => $pixvoteHelper->getProfileUrl($customer)
		);
		$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_DESIGN_APPROVED, $customer->getEmail(), $vars);
	}
	
	function _sendRejection($customerId, $rejectionId, $challengeId)
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$rejection =  Mage::getModel($pixvoteHelper->REJECTIONS_MODEL_PATH)->load($rejectionId);
		$challenge =  Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($challengeId);
		$customer = Mage::getModel("customer/customer")->load($customerId);
		$emailHelper = Mage::helper("bevel/email");
		$vars = array(
			"not_approved_reason" => $rejection->getReason(),
			"not_approved_explanation" => $rejection->getExplanation(),
			"approval_faq" => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."faq",
			"challenge_url" => $pixvoteHelper->getChallengeAboutUrl($challenge)
		);
		$emailHelper->sendEmail($emailHelper->EMAIL_TEMPLATE_DESIGN_NOT_APPROVED, $customer->getEmail(), $vars);
	}
	
	/**
	 * Export customer grid to CSV format
	 */
	public function exportCsvAction()
	{
		$pixvoteHelper = Mage::helper("pixvote");
		$now = str_replace(" ","_", $pixvoteHelper->now());
		$fileName   = 'submission.csv';
		$content    = $this->getLayout()->createBlock('pixvote/adminhtml_entry_grid')->getCsvFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
		
	
}
?>
