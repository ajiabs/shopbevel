<?php
	class Pixafy_Pixvote_ViewController extends Mage_Core_Controller_Front_Action 
	{
		function indexAction()
		{
			$id = str_replace(Mage::helper("pixvote")->CHALLENGE_SEPARATOR," ",$this->getRequest()->getParam("id"));
			$challenge = Mage::getModel(Mage::helper("pixvote")->CHALLENGES_MODEL_PATH)->load($id,'name');
			if(!$challenge->getId() || !$challenge->getIsVisible())
			{
				Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__("Challenge not found"));
				return $this->_redirect('');
			}
			else
			{
				Mage::register(Mage::helper("pixvote")->CURRENT_CHALLENGE_KEY, $challenge);
				$this->loadLayout();
				$this->renderLayout();
			}
		}
		
		function submissionAction()
		{
			$id = $this->getRequest()->getParam("id");
			if(!$id)
			{
				Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__("Submission not found"));
				return $this->_redirect('');
			}
			else
			{
				$this->loadLayout();
				$this->renderLayout();
			}
		}
		
		function moodBoardAction()
		{
			$url = $this->getRequest()->getParam("url");
			$json  = array();
			if(!$url || stripos($url,'pinterest.com') === false)
			{
				//Error
				$json['error'] = '';
			}
			else
			{
				$json['html'] =  Mage::helper("pixvote")->getMoodBoard($url);
			}
			echo json_encode($json);
		}
	}
?>
