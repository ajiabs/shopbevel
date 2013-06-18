<?php


class Pixafy_Registeration_LinkController extends Mage_Core_Controller_Front_Action{
	
	public function processAction(){
		$urlkey = $this->getRequest()->getParam('invite');
		$profile = Mage::getModel('registeration/waitlist')->loadByKey($urlkey);
		if(!!$profile && $profile->getId()){
			//$record = Mage::getModel('registeration/invitelink')->loadByKey($urlkey)
		// update the profile (rank,count
			$profile->setWaitRank($profile->getWaitRank()-1);
			$profile->save();
		}
		$this->_redirect(Mage::getBaseUrl());
		return;
	}	
}
