<?php


class Pixafy_Registeration_AjaxController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction(){
		$this->loadLayout();		
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rpone')->toHtml();	
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	
	public function pagetwoAction(){		
		$this->loadLayout();
		//$this->renderLayout();
	
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rptwo')->toHtml();
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	
	public function pagethreeAction(){	
		$this->loadLayout();
		//$this->renderLayout();
	
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rpthree')->toHtml();
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	
	public function pagefourAction(){
		$this->loadLayout();
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rpfour')->toHtml();
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	

	
	public function pagefiveAction(){
		$this->loadLayout();
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rpfive')->toHtml();
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	
	public function pagesixAction(){
		
		parse_str($this->getRequest()->getParam("data"), $data);
		$this->loadLayout();
		$email = $data['email'];
		$flag = Mage::helper('registeration')->verifyEmail($email);
		if( $flag){		
			Mage::getsingleton('core/session')->setPreRegisterationEmail($email);
			echo $this->getLayout()->getBlock('registeration_rpsix')->toHtml();
		}else{
			echo $this->getLayout()->getBlock('registeration_rpfive')->toHtml();
		}
		exit;
	}
	
	public function pagesevenAction(){

		parse_str($this->getRequest()->getParam("data"), $data);
		$this->loadLayout();		
		if(true){
			try{
				$rank = Mage::helper('registeration')->createRank();
				$profile = Mage::getModel('registeration/waitlist');
				$profile->setCustEmail($data['email']);
				$profile->setFirstName($data['firstname']);
				$profile->setLastName($data['lastname']);
				$profile->setGender((int)$data['gender']);
				$profile->setPassword($data['password']);
				$profile->setWaitRank($rank);
				$profile->save();
			}catch(Exception $e){
				echo $e->getMessage();
			}
			
			$link = Mage::helper('registeration')->createUniqueLink($data['email']);
			Mage::getsingleton('core/session')->setUniqueLink($link);
			Mage::getsingleton('core/session')->setWaitlistRank($rank);
		}
		
		echo $this->getLayout()->getBlock('registeration_rpseven')->toHtml();
		exit;
	}
	
	public function pageeightAction(){
		$this->loadLayout();
		$jsonResponse['html_content'] = $this->getLayout()->getBlock('registeration_rpeight')->toHtml();
		$jsonResponse['status'] = 1;
		$jsonResponse['error_message'] = '';
	
		echo json_encode($jsonResponse);
		exit;
	}
	
	public function saveAction(){
		$data = $this->getRequest()->getParams();
		
	}
	
	//Function to register user via ajax
	public function register_userAction()
	{
		$data = $this->getRequest()->getParams();
		$customerEntityType = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
		$storeId = Mage::app()->getStore()->getId();
		$json = array();
	    $websiteID = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteID);
	    $customer->loadByEmail($data['email']);
		if(!$customer->getId()) 
		{
	        $groupID = '1';
	        $customer->setData( 'group_id', $groupID );
	        $customer->setEmail($data['email']);
	        $customer->setGender($data['gender']);
	        $customer->setAge($data['age']);
	        $customer->setZip($data['zipcode']);
	        $customer->setFirstname($data['first-name']);
	        $customer->setLastname($data['last-name']);
	        $customer->setPasswordHash(md5($data['password']));
			if (!empty($data['fb-id'])) {
				$customer->setFbId($data['fb-id']);
			}
	        $customer->setConfirmation(null);
	        $customer->setWebsiteId($websiteID);
	        $customer->setIsSubscribed(1);
	        $customer->save();
			$customer->sendNewAccountEmail('registered', '', $storeId);
			// Subscribe the customer
			try
			{
				//Login user
				$session = Mage::getSingleton('customer/session');
				$session->login($data['email'], $data['password']);
			    /*if ($session->getCustomer()->getIsJustConfirmed()) {
			         //Customer is confirmed and successfully logged in
			     }	
				*/
				$json['redirect_url'] = Mage::getUrl('customer/account/');
				
			}
			catch(Exception $e)
			{
				$session->addError($e->getMessage());
				$json['error'] = 'An error has occured. Please try again';
			}
		}
		else
		{
			$json['error'] = 'That email has already been used';
		}
		echo json_encode($json);
	}
	
	
	
	
	
	
	
	
	
	
}
