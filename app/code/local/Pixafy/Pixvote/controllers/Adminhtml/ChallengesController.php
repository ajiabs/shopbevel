<?php
	class Pixafy_Pixvote_Adminhtml_ChallengesController extends Mage_Adminhtml_Controller_Action
	{
		const CHALLENGES_MODEL_PATH = "pixvote/challenges";
		function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenuchallenges/view_challenges');
			$this->renderLayout();
		}
		
		function newAction()
		{
			$this->_forward('edit');
		}
		
		function editAction()
		{
			$id = $this->getRequest()->getParam("id");
			$challenge = mage::getModel(self::CHALLENGES_MODEL_PATH)->load($id);
			if(is_object($challenge))
			{
				Mage::register('challenge_data', $challenge);
				$this->loadLayout();
				$this->_setActiveMenu('pixvotemenuchallenges/edit_challenge');
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
			$challengeData = $this->getRequest()->getParam("challenge");
			if($challengeData)
			{
				try
				{
					$challenge = mage::getModel(self::CHALLENGES_MODEL_PATH)->load($challengeData['id']);

					//if working with a new record, unset id 
					if(!$challenge->getId())
					{
						unset($challengeData['id']);
					}

					$default = '0000-00-00 00:00:00';
					if(!$challengeData['submission_start_time'])
					{
						unset($challengeData['submission_start_time']);
					}

					if(!$challengeData['submission_end_time'])
					{
						unset($challengeData['vote_end_time']);
					}
					
					if(!$challengeData['vote_start_time'])
					{
						unset($challengeData['vote_start_time']);
					}

					if(!$challengeData['vote_end_time'])
					{
						unset($challengeData['vote_end_time']);
					}
					$challenge->addData($challengeData);
					if($challenge->save())
					{
						//Try to upload image for challenge
						$folderName =  Mage::helper("pixvote")->CHALLENGE_IMAGE_FOLDER.$challenge->getId();
						$media_folder = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$folderName;
						if (!is_dir($media_folder))
						{
							mkdir($media_folder, 0775, true);
						}
						$input_name = "challenge-image";
						$deleteImages = $this->getRequest()->getParam("challenge-image");
						//var_dump($_FILES);exit;
						for($i = 0; $i < count($_FILES[$input_name]['name']); $i++) 
						{	
							$fileInfo = Mage::helper("pixvote")->uploadImage($i,$input_name, $media_folder);
							if($fileInfo)
							{
								extract($fileInfo);
								$oldImage = $challenge->getImage();
								$challenge->setImage($folderName.DS.$server_name);
								$challenge->save();
								if(isset($deleteImages[$i]['delete']))
								{
									unlink(Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$oldImage);
								}
							}
						}
						//Mage::dispatchEvent('pixafy_pixvote_entry_saved', array('entry' => $entry));
						$action = isset($challengeData['id']) ? "updated" : "created";
						$message = "Challenge {$action}";
						$method = "addSuccess";
						
						//Create rewrites for challenge about and vote pages
						$name = strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $challenge->getName()));
						$idPath = "challenges/".$challenge->getId();
						$targetPath = Mage::helper("pixvote")->FRONT_NAME."/view/index/id/".$name;
						$requestPath = 'challenges/'.$name;
						$this->_createRewriteUrl($challenge, $idPath, $targetPath, $requestPath);
						
						$idPath = "vote/".$challenge->getId();
						$targetPath = Mage::helper("pixvote")->FRONT_NAME."/index/index/id/".$challenge->getId();
						$requestPath = 'vote/'.$name;
						$this->_createRewriteUrl($challenge, $idPath, $targetPath, $requestPath);
						
						Mage::getSingleton('adminhtml/session')->setChallengeData(false);
					}
					else
					{
						$message = "An error occured while creating the challenge";
						$method = "addError";
					}
					Mage::getSingleton('adminhtml/session')->$method(Mage::helper('adminhtml')->__($message));
					if ($this->getRequest()->getParam('back')) 
					{
						$this->_redirect('*/*/edit', array('id' => $challenge->getId()));
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

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Challenge was successfully deleted'));
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
				$this->getLayout()->createBlock('pixvote/adminhtml_challenges_grid')->toHtml()
			);
		}
		
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
		
		/**
		 * Export customer grid to CSV format
		 */
		public function exportCsvAction()
		{
			$id = $this->getRequest()->getParam("id");
			$challenge = mage::getModel(self::CHALLENGES_MODEL_PATH)->load($id);
			$name = strtolower(str_replace(" ",Mage::helper("pixvote")->CHALLENGE_SEPARATOR, $challenge->getName()));
			Mage::register('challenge_data', $challenge);
			$fileName   = 'challenge-'.$name.'-voter-email.csv';
			$content    = $this->getLayout()->createBlock('pixvote/adminhtml_challenges_edit_tab_voters')->getCsvFile();
			$this->_prepareDownloadResponse($fileName, $content);
		}
		
		
		function voterGridAction()
		{
			$challenge = $this->_loadChallenge();
			Mage::register('challenge_data', $challenge);
			$this->getResponse()->setBody(
					$this->getLayout()->createBlock('pixvote/adminhtml_challenges_edit_tab_voters')->toHtml()
			);
		}
		
		private function _loadChallenge()
		{
			$id = $this->getRequest()->getParam("id");
			$challenge = mage::getModel(self::CHALLENGES_MODEL_PATH)->load($id);
			return $challenge;
		}
	}
?>