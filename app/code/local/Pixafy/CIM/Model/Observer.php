<?php
class Pixafy_CIM_Model_Observer
{
    //
    //	Flags to make sure functions only get called once
    //
    private $helper;
    static protected $_singletonFlagCim 	= 	false;
    static protected $_singletonFlagSave 	= 	false;
    public function createCimProfile(Varien_Event_Observer $observer, $orderCustomer=NULL){
        if (!self::$_singletonFlagCim) {
            self::$_singletonFlagCim = true;		
            $customer = $observer->getEvent()->getCustomer();
            //
            //	Ensure both CIM Method and Authorize.net method are enabled
            //
            if(Mage::getStoreConfig('payment/cimmethod/active') && Mage::getStoreConfig('payment/authorizenet/active')){
				//
				//	If the is_new value is not present then
				//	we know it is the initial signup.
				//
				//	Create CIM account
				//
				if(!$customer->getData('cim_created')){
					$helper					=	Mage::helper('cim/data');
					$customer				=	$helper->createCustomerProfile($customer);
					$customer->save();
				}
			}
		}
	}

	public function saveCreditCard(Varien_Event_Observer $observer){
        if (!self::$_singletonFlagSave) {
            self::$_singletonFlagSave = true;
            //
            //	If both the CIM and Authorize.net gateways are active
            //
            if(Mage::getStoreConfig('payment/cimmethod/active') && Mage::getStoreConfig('payment/authorizenet/active')){
				$order  		= 	$observer->getEvent()->getOrder();
				$helper 		= 	Mage::helper('cim');
				$this->helper	=	$helper;
				$post			=	$this->getPost();
				
				//
				//	If the customer is logged in then it is not a new user
				//	Therefore we can obtain the CIM Customer Id directly
				//	from the $order->getCustomer() function
				//
				//	If the customer is not logged in then the user has registered during
				//	during the checkout process. Because of this, $order->getCustomer() does
				//	not contain the CIM Customer Id, so we need to reload the customer model
				//	to obtain what we need
				//
				if(Mage::getSingleton('customer/session')->isLoggedIn()){
					$customer = $order->getCustomer();
				}
				else{
					$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
				}
				
				//
				//	If there are payment related details in the POST data
				//
				if(isset($post['payment'])){
					$payment = $post['payment'];
					if(isset($payment['cim_save_card']) || Mage::getStoreConfig('payment/cimmethod/card_autosave')){
						//
						// Confirm authorize.net is the payment method being used
						//
						if($payment['method'] == $helper->CC_METHOD_AUTHORIZENET){
							$billingData = $order->getBillingAddress()->getData();
							
							//
							// 	If the credit card number or expiration date is not present
							//	then throw an error
							//
							if($payment['cc_number'] && $payment['cc_exp_month'] && $payment['cc_exp_year']){
								
								//
								//	Switch format MYYYY to MMYYYY
								//
								$payment['cc_exp_month'] = $helper->adjustLength($payment['cc_exp_month']);
								
								//
								//	Add the payment data to our billing data array
								//
								$billingData+=$payment;
							}
							else{
								Mage::throwException('Please fill out credit card number and expiration date');
							}
							
							//
							//	Check and see if the credit card information is already saved.
							//	Search for the last four digits and card type provided from POST
							//	data for a given customer id. If the card is found, then the same card
							//	has been entered. Regardless of whether or not this card has the same
							//	date, delete the payment profile and recreate with (possibly) new card data
							//
							//	This will handle a scenario in which a user enters the same credit card number
							//	with a new expiration date
							//
							$lastFour 	= 	substr($payment['cc_number'], -4);
							$cardType 	= 	$helper->CREDIT_CARD_MAPPINGS[$payment['cc_type']];
							$card		=	Mage::getResourceModel('cim/paymentprofiles_collection');
							
							$card->addFieldToFilter('customer_id', $order->getCustomerId());
							$card->addFieldToFilter('cc_lastfour', $lastFour);
							$card->addFieldToFilter('cc_type', $cardType);
							
							if($customer->getCimId()){
								// If no card found, then create the payment profile for it
								if(!count($card)){
									$this->createNewCard($billingData, $customer, $lastFour, $payment, $cardType);
								}
								else{
									foreach($card as $c){
										$delResult = $helper->paymentProfileRequest($customer->getCimId(), $c->getProfileId(), $helper->_deleteProfileRequest);
										if($delResult['code'] == $helper->RESPONSE_CODE_SUCCESS){
											try{
												$cardToDelete	=	Mage::getModel('cim/paymentprofiles')->load($c->getId());
												if($cardToDelete->getId()){
													$cardToDelete->delete();
												}
												$this->createNewCard($billingData, $customer, $lastFour, $payment, $cardType);
											}
											catch(Exception $e){
												Mage::log($e->getMessage());
												Mage::throwException(Mage::helper('cim')->__('Unable to place order using provided credit card'));
											}
										}
									}
								}
							}
							else{
								Mage::log($helper->ERROR_MESSAGE_NO_CUSTOMER_CIM.$customer->getEmail());
							}
						}
					}
				}
			}            
		}	
	}
	
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    private function getPost(){
		return Mage::app()->getRequest()->getPost();
	}
	
	private function createNewCard($billingData, $customer, $lastFour, $payment, $cardType){
		$billingData['street'] =	implode(", ", explode("\n", $billingData['street']));
		$billingData['cim_id'] =	$customer->getCimId();
		if($result = $this->helper->createPaymentProfile($billingData)){
			$newCard	=	Mage::getModel('cim/paymentprofiles');
			$newCard->setCustomerId($customer->getId());
			$newCard->setCcLastfour($lastFour);
			$newCard->setCcExpMonth($payment['cc_exp_month']);
			$newCard->setCcExpYear($payment['cc_exp_year']);
			$newCard->setCcType($cardType);
			$newCard->setProfileId($result['paymentId']);
			$newCard->setCimId($billingData['cim_id']);
			$newCard->save();
		}
		else{
			Mage::log($result);
			Mage::throwException(Mage::helper('cim')->__('Unable to place order using provided credit card'));
		}
		return TRUE;
	}
}
?>
