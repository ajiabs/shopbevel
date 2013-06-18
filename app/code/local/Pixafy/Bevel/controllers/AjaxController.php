<?php


class Pixafy_Bevel_AjaxController extends Mage_Core_Controller_Front_Action{
	
	//Function to register user via ajax
	public function register_userAction()
	{
		require_once("scripts/facebook/src/facebook.php");
		$session = $this->_getSession();
		$data = $this->getRequest()->getParams();
		$customerEntityType = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
		$storeId = Mage::app()->getStore()->getId();
		$json = array();
	    $websiteID = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteID);
	    $customer->loadByEmail($data['email']);
		$refererUrl = $this->_getRefererUrl();
		$json['redirect_url'] = $refererUrl == Mage::getBaseUrl() ?  Mage::getUrl('shop') : $refererUrl;
		if(!$customer->getId()) 
		{
	        $groupID = '1';
	        $customer->setData( 'group_id', $groupID );
	        $customer->setEmail($data['email']);
	        //$customer->setGender($data['gender']);
	        //$customer->setAge($data['age']);
	        //$customer->setZip($data['zipcode']);
	        $customer->setFirstname($data['first-name']);
	        $customer->setLastname($data['last-name']);
	        $customer->setPasswordHash(md5($data['password']));
			if (!empty($data['fb-id'])) {
				$customer->setFbId($data['fb-id']);
			}
			if (!empty($data['is-designer'])) {
				$customer->setIsDesigner($data['is-designer']);
			}
	        $customer->setConfirmation(null);
	        $customer->setWebsiteId($websiteID);
	        $customer->setIsSubscribed(1);
	        $customer->save();
			$customer->sendNewAccountEmail('registered', '', $storeId);
			$session->setCustomerAsLoggedIn($customer);
			/*
			$customer->sendNewAccountEmail('registered', '', $storeId);
			// Subscribe the customer
			try
			{
				//Login user
				$session = Mage::getSingleton('customer/session');
				$session->login($data['email'], $data['password']);
//			    if ($session->getCustomer()->getIsJustConfirmed()) {
//			         //Customer is confirmed and successfully logged in
//			     }	
//				
				$json['redirect_url'] = Mage::getUrl('customer/account/');
				
			}
			catch(Exception $e)
			{
				$session->addError($e->getMessage());
				$json['error'] = 'An error has occured. Please try again';
			}
			 */
		}
		else
		{
			//check if the customer has the fb id
			if(!empty($data['fb-id']))
			{
				$config = array();
				$config['appId'] = Mage::helper('bevel')->_fb_app_id;
				$config['secret'] =  Mage::helper('bevel')->_fb_app_secret;
				$facebook = new Facebook($config);
				//Confirm that the fb users email is the customer's email
				$user_profile = $facebook->api('/me','GET');
				if(isset($user_profile['email']) && $user_profile['email'] == $customer->getEmail())
				{
					$customer->setFbId($user_profile['id']);
					$customer->save();
					$session->setCustomerAsLoggedIn($customer);
//					$json['refresh']= true;
					$json['logged_in'] = true;
				}
			}
			else
			{
				$json['error'] = $this->__('That email has already been used');
			}
		}
		
		if(!isset($json['error']) && $data['design-id'])
		{
			//redirect to design submission
			$pixvoteHelper = Mage::helper("pixvote");
			$rewriteUrl = $pixvoteHelper->getDesignRewriteUrl($data['design-id']);
			if($rewriteUrl)
			{
				$json['redirect_url']  = $rewriteUrl;
			}
		}
		
		echo json_encode($json);
	}
	
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
