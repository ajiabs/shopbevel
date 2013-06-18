<?php
class Pixafy_CIMMethod_Model_Cimmethod extends Mage_Payment_Model_Method_Free
{
    
    
    //
    //	The block which displays the dropdowns
    //
    protected $_formBlockType = 'cim/payment_stored';

    //
    //	Block that displays cc type, exp, etc after order is placed
    //
    protected $_infoBlockType = 'cim/payment_info';
    
    
    
    //
    //	Unique code for method
    //
    protected $_code = 'cimmethod';
 
    //
    //	Various booleans that determine functionality
    //
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
 
    // Admin flag
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc 				= false; //cc numbers are saved on authorize.net's servers, not locally
 
    public function isAvailable($quote = null)
    {
		$helper = Mage::helper('cim');
		if(Mage::getStoreConfig('payment/cimmethod/active') &&  Mage::getStoreConfig('payment/authorizenet/active') && Mage::getStoreConfig('payment/cimmethod/savedcard_placeorders')){
			$customerId = (Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getId() : $quote->getCustomerId());
			if(!$customerId){
				return FALSE;
			}
			$collection	=	Mage::getModel('cim/paymentprofiles')->getCollection()->addFilter('customer_id', $customerId);
			if(count($collection)){
				$savedCards	=	'<select name="payment[profileId]" id="cim_savedcards" class="required-entry"><option value="">'.Mage::helper('cim')->__('Please choose saved card').'</option>';
				
				$htmlPaymentInfo = '';
					$htmlPaymentInfo.='
					<input type="hidden" name="payment[cc_last4]" value="" id="cim_cc_last4" />
					<input type="hidden" name="payment[cc_type]" value="" id="cim_cc_type" />
					<input type="hidden" name="payment[cc_exp_month]" value="" id="cim_cc_exp_month"/>
					<input type="hidden" name="payment[cc_exp_year]" value="" id="cim_cc_exp_year"/>';
				foreach($collection as $cardInfo){
					$encId = Mage::getSingleton('core/encryption')->encrypt($cardInfo->getId());
					$htmlPaymentInfo.='
					<input type="hidden" value="'.$cardInfo->getCcLastfour().'" id="cim_cc_last4_'.$encId.'" />
					<input type="hidden" value="'.$cardInfo->getCcType().'" id="cim_cc_type_'.$encId.'" />
					<input type="hidden" value="'.$cardInfo->getCcExpMonth().'" id="cim_cc_exp_month_'.$encId.'"/>
					<input type="hidden" value="'.$cardInfo->getCcExpYear().'" id="cim_cc_exp_year_'.$encId.'"/>';
					$savedCards.='
					<option value="'.$encId.'">'.Mage::helper('cim')->__($cardInfo->getCcType()).' '.Mage::helper('cim')->__('ending in ').$cardInfo->getData('cc_lastfour').'</option>';
				}
				$savedCards.='</select>';
				$savedCards.=$htmlPaymentInfo;
				if(!Mage::registry($helper->REGISTRY_KEY_SAVED_CARDS)){
					Mage::register($helper->REGISTRY_KEY_SAVED_CARDS, $savedCards);
				}
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
    }     
	public function capture(Varien_Object $payment, $amount){
		$helper = Mage::helper('cim');
		$post = (Mage::app()->getRequest()->getPost());
		if($post['payment']['method'] == $helper->PAYMENT_METHOD_CIMMETHOD){
			$toCharge	=	TRUE;
			$customerId = (Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getId() : $payment->getOrder()->getCustomerId());
			if($customerId){
				$profileId		=	Mage::getSingleton('core/encryption')->decrypt($post['payment']['profileId']);
				$profileModel	=	Mage::getModel('cim/paymentprofiles')->load($profileId);
				if(!$profileModel->getId()){
					Mage::throwException('There was an error while processing your order.');
					$toCharge	=	FALSE;
				}
				if($customerId	!=	$profileModel->getCustomerId()){
					Mage::throwException('There was an error while processing your order.');
					$toCharge	=	FALSE;
				}
				if($toCharge){
					$result	=	$helper->processPaymentWithStoredInfo($amount, $profileModel->getCimId(), $profileModel->getProfileId());
					if(trim(strtolower($result['text']))	==	'successful.'){
						//	order placed successfully
						$payment->setData('cc_type', $profileModel->getData('cc_type'));
						$payment->setData('cc_last4', $profileModel->getData('cc_lastfour'));
						$payment->setData('cc_exp_month', $profileModel->getCcExpMonth());
						$payment->setData('cc_exp_year', $profileModel->getCcExpYear());
						$payment->save();
					}
					else{
						Mage::throwException('There was an error while processing your order: '.$result['text']);
					}
				}
			}
			else{
				Mage::throwException('You must be logged in');
			}
		}
		
	}
}
?>
