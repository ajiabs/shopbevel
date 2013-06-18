<?php
    class Pixafy_CIM_IndexController extends Mage_Core_Controller_Front_Action 
    {
        function indexAction()
        {
            $this->_checkLogin();
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $block = $this->getLayout()->getBlock('cim_cards');
            $block->loadProfiles();
            $this->renderLayout();
        }
        
        public function editAction()
        {
            $this->_checkLogin();
			
			$profile = $this->_getProfileFromParam();
			
			if (!$profile) {
				return $this->_redirect('*');
			}
			
			$method = null;
			/*
			 * Check if the customer is actually the owner of this card
			 */
			if (Mage::helper("customer")->getCurrentCustomer()->getId()
					== $profile->getCustomerId()) {

				$profilePostInfo = $this->getRequest()->getParam("card");
				if($profilePostInfo)
				{
					//Process edit action
					$cimHelper = Mage::helper("cim");
					$profilePostInfo['cim_id'] = $profile->getCimId();
					$profilePostInfo['profile_id'] = $profile->getProfileId();
					$result = $cimHelper->updatePaymentProfile($profilePostInfo);
					if($result['code'] == $cimHelper->RESPONSE_CODE_SUCCESS)
					{
						$method = "addSuccess";
						$msg = "Your card has been updated";

						//Update profile Record
						if($profilePostInfo['cc_exp_month'] != "XX" && $profilePostInfo['cc_exp_month'] != "XX")
						{
							$profile->setCcExpMonth($profilePostInfo['cc_exp_month']);
							$profile->setCcExpYear($profilePostInfo['cc_exp_year']);
						}
						$profile->setCcLastfour(substr($profilePostInfo['cc_number'], -4));
						$profile->save();
					}
					else
					{
						$method = "addError";
						$msg = "Error: ".$result['code'] .' - problem updating card.';
					}
				}
			} else {
				$method = "addError";
				$msg = "Error: You are not the owner of this card!";
			}
				
			if ($method) {
				$this->_getCustomerSession()->$method(Mage::helper('customer')->__($msg));
			}
			
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $block = $this->getLayout()->getBlock('cim_cards_edit');
            $block->loadProfileInfo($profile);
            $this->renderLayout();
        }
        
		public function addAction() {
			$this->_checkLogin();

			$profilePostInfo = $this->getRequest()->getParam("card");
			$profile = Mage::getModel('cim/paymentprofiles');
			
			$error = false;
			
            if($profilePostInfo) {
                $cimHelper = Mage::helper("cim");
				$customer = Mage::helper('customer')->getCustomer();
				$cimId = $customer->getCimId();

				/*
				 * If we do not have a CIM, create a new account with auth.net
				 */
				if (!$cimId) {
					try {
						$customer = $cimHelper->createCustomerProfile($customer);
						$customer->save();
						$cimId = $customer->getCimId();

					} catch (Exception $e) {
						$error = true;
						$method = "addError";
						$msg = "Error: Problem creating new profile.)";
					}
				} 
				$profile->setCustomerId($customer->getId());
				$profile->setCimId($cimId);
				
				/*
				 * If we have a good account, create a new 
				 * payment profile with auth.net
				 */
				if (!$error) {
					$profilePostInfo['cim_id'] = $profile->getCimId();

					$result = $cimHelper->createPaymentProfile($profilePostInfo);
					
					if($result['code'] == $cimHelper->RESPONSE_CODE_SUCCESS) {
						$method = "addSuccess";
						$msg = "Your card has been added";

						/*
						 * Update profile Record
						 */
						if($profilePostInfo['cc_exp_month'] != "XX" 
							&& $profilePostInfo['cc_exp_month'] != "XX") {
							$profile->setCcExpMonth($profilePostInfo['cc_exp_month']);
							$profile->setCcExpYear($profilePostInfo['cc_exp_year']);
						}
						
						$profile->setCcLastfour(substr($profilePostInfo['cc_number'], -4));
						$profile->setProfileId($result['paymentId']);

						try {
							$profile->save();
						} catch (Exception $e) {
							$error = true;
							$method = "addError";
							$msg = "Error: Problem creating new profile.";
						}
					}
					else {
						$error = true;
						$method = "addError";
						$msg = "Error: " . $result['code']. " - Unable to add card. Please check entered information.";
					}
				}
				if ($method) {
					$this->_getCustomerSession()->$method(Mage::helper('customer')->__($msg));
				}
				
				if (!$error) {
					return $this->_redirect('*');
				}
			}
			
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
			$block = $this->getLayout()->getBlock('cim_cards_add');
            $this->renderLayout();
		}
		
		/*
		 * deletes a card profile
		 */
        public function deleteAction()
        {
            $this->_checkLogin();
            $profile = $this->_getProfileFromParam();
			if (!$profile) {
				return $this->_redirect('*');
			}
			
			if (Mage::helper("customer")->getCurrentCustomer()->getId()
					== $profile->getCustomerId())
			{
				try {
					$profile->delete();
				} catch (Exception $e) {
					Mage::logException($e->getMessage());
				}
	            $this->_getCustomerSession()->addSuccess(Mage::helper('customer')->__("Card removed"));
			}else {
				$this->_getCustomerSession()->addError(Mage::helper('customer')->__("Error: You are not the owner of this card!"));
			}
            return $this->_redirect('*');
        }
        
		/*
		 * grabs the profile model from the url post parameter
		 */
        protected function _getProfileFromParam()
        {
		    $id = Mage::getSingleton('core/encryption')->decrypt($this->getRequest()->getPost("id"));
            $profile = Mage::getModel("cim/paymentprofiles")->load($id);
			
            if(!$profile->getId() || $profile->getCustomerId() != Mage::helper("customer")->getCustomer()->getId())
            {
                $this->_getCustomerSession()->addError(Mage::helper('customer')->__("Record not found for the provided id"));
				return false;
            }
            else
            {
                return $profile;
            }
        }
        
        protected function _getCustomerSession()
        {
            return Mage::getSingleton('customer/session');
        }

        protected function _checkLogin()
        {
            if(!Mage::helper("customer")->isLoggedIn())
            {
                 $this->_getCustomerSession()->addNotice(Mage::helper('customer')->__("Please Login."));
                return $this->_redirect('customer/account/login');
            }
        }
	}
