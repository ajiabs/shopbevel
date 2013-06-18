<?php
	class Pixafy_Pixvote_Adminhtml_VotesController extends Mage_Adminhtml_Controller_Action
	{
		public function indexAction()
		{
			$this->loadLayout();
			$this->_setActiveMenu('pixvotemenuchallenges/challenge_votes');
			$this->renderLayout();
		}
		
		public function gridAction()
		{
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('pixvote/adminhtml_votes_grid')->toHtml()
			);
		}
		
		public function viewAction()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$challenge = $this->_loadChallenge();
			if(is_object($challenge))
			{
				Mage::register($pixvoteHelper->REGISTRY_CHALLENGE_DATA, $challenge);
				$this->loadLayout();
				$this->renderLayout();
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addError($pixvoteHelper->__('Record not found'));
				$this->_redirect('*/*/');
			}
		}
		
		public function voterGridAction()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$challenge = $this->_loadChallenge();
			Mage::register($pixvoteHelper->REGISTRY_CHALLENGE_DATA, $challenge);
			$this->getResponse()->setBody(
					$this->getLayout()->createBlock('pixvote/adminhtml_votes_edit_tab_voters')->toHtml()
			);
		}
		
		public function exportCsvAction()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$challenge = $this->_loadChallenge();
			$name = strtolower(str_replace(" ",$pixvoteHelper->CHALLENGE_SEPARATOR, $challenge->getName()));
			Mage::register('challenge_data', $challenge);
			$fileName   = 'challenge-'.$name.'-voter-email.csv';
			$content    = $this->getLayout()->createBlock('pixvote/adminhtml_votes_edit_tab_voters')->getCsvFile();
			$this->_prepareDownloadResponse($fileName, $content);
		}
		
		private function _loadChallenge()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$id = $this->getRequest()->getParam("id");
			$challenge = Mage::getModel($pixvoteHelper->CHALLENGES_MODEL_PATH)->load($id);
			return $challenge;
		}
	}
?>