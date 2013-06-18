<?php
class Pixafy_CIM_Helper_Data extends Mage_Core_Helper_Abstract {
	
	
	
	public 	$RESPONSE_CODE_DUPLICATE_ACCOUNT 	= 	'E00039';								//	Response code from Authorize.net indicating the customer profile already exists
	public 	$RESPONSE_CODE_SUCCESS				=	'I00001';								//	Response code from Authorize.net indicating the operation was successful
	public 	$CC_METHOD_AUTHORIZENET				=	'authorizenet';							//	Magento standard payment method name for Authorize.net
	public 	$PAYMENT_METHOD_CIMMETHOD			=	'cimmethod';							//	Pixafy payment method name for the CIM payment method
	public 	$REGISTRY_KEY_SAVED_CARDS			=	'pixcim_saved_cards';					//	The registry key for saved cards so the data does not need to be requeried
	public 	$_deleteProfileRequest				=	'deleteCustomerPaymentProfileRequest';	//	XML tag telling Authorize.net CIM Api to the delete the payment profile
	public 	$_getProfileRequest					=	'getCustomerPaymentProfileRequest';		//	XML tag telling Authorize.net CIM Api to the retreive the payment profile
	public  $MONTHS = array(
               "01" => "January",
               "02" => "February",
               "03" => "March",
               "04" => "April",
               "05" => "May",
               "06" => "June",
               "07" => "July",
               "08" => "August",
               "09" => "September",
               "10" => "October",
               "11" => "November",
               "12" => "December",
        );
        public $YEAR_LIMIT = 10;
	
	public 	$isTestMode;																	//	Flag indicating whether or not Authorize.net module is in test mode
	public 	$_apiId;																		//	Authorize.net Api Login Id
	public 	$_transKey;																		//	Authorize.net Transaction key
	public	$_customer;	
	public function __construct(){
		$this->_apiId		=	Mage::getStoreConfig('payment/authorizenet/login');
		$this->_transKey	=	Mage::getStoreConfig('payment/authorizenet/trans_key');
		$this->isTestMode	=	Mage::getStoreConfig('payment/authorizenet/test');
		
	}
	public function merchantAuthenticationBlock()
	{
		return "<merchantAuthentication>".
			"<name>" . $this->_apiId . "</name>".
			"<transactionKey>" . $this->_transKey . "</transactionKey>".
			"</merchantAuthentication>";
	}	
	
	public function createCustomerProfile($customer)
	{
		$content =
		"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		"<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
		$this->merchantAuthenticationBlock().
		"<profile>".
		"<merchantCustomerId>".$customer->getId()."</merchantCustomerId>". // Magento customer id
		"<description></description>".
		"<email>" . $customer->getEmail(). "</email>".
		"</profile>".
		"</createCustomerProfileRequest>";
		$array					=	$this->getResponseArray($this->send_request_via_curl($content));
		if($array['code'] == $this->RESPONSE_CODE_DUPLICATE_ACCOUNT){
			$profileId = '';
			$text = $array['text'];
			for($x=0; $x<strlen($text); $x++){
				if(is_numeric($text{$x})){
					$profileId.=$text{$x};
				}
			}
			$array['id'] 		=	$profileId;
			$array['code'] 		=	$this->RESPONSE_CODE_SUCCESS;
		}
		if(($array['code']	==	$this->RESPONSE_CODE_SUCCESS)	&&	(trim($array['id']))){
			$customer->setCimId($array['id']);
			$customer->setCimCreated(1);
		}
		return $customer;
	}
	
	//function to send xml request via curl
	public function send_request_via_curl($content)
	{
		//
		//	Uses authorize.net modules test mode system config
		//
		if($this->isTestMode){
			$posturl = "https://apitest.authorize.net/xml/v1/request.api";
		}
		else{
			$posturl = "https://api.authorize.net/xml/v1/request.api";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		return $response;
	}
	
	//helper function for parsing response
	public function substring_between($haystack,$start,$end) 
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
		{
			return false;
		} 
		else 
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
	public function createPaymentProfile($billingData)
	{
		$content =
		"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		"<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
		$this->merchantAuthenticationBlock().
		"<customerProfileId>".$billingData['cim_id'] ."</customerProfileId>".
		"<paymentProfile>".
		"<billTo>".
		 "<firstName>".$billingData['firstname']."</firstName>".
		 "<lastName>".$billingData['lastname']."</lastName>".
		 "<address>".$billingData['street']."</address>
			<city>".$billingData['city']."</city>
			<state>".$billingData['region']."</state>
			<zip>".$billingData['postcode']."</zip>".
		 "<phoneNumber></phoneNumber>".
			"<faxNumber></faxNumber>".
		"</billTo>".
		"<payment>".
		 "<creditCard>".
		  "<cardNumber>".$billingData['cc_number']."</cardNumber>".
		  "<expirationDate>".$billingData['cc_exp_year']."-".$this->adjustLength($billingData['cc_exp_month'])."</expirationDate>". // required format for API is YYYY-MM
		 "</creditCard>".
		"</payment>".
		"</paymentProfile>".
		"<validationMode>testMode</validationMode>". // or testMode
		"</createCustomerPaymentProfileRequest>";
		
		$array	=	$this->getResponseArray($this->send_request_via_curl($content));
		if($array['code'] == $this->RESPONSE_CODE_SUCCESS && trim($array['paymentId'])){
			return $array;
		}
		else{
			if($array['code'] == $this->RESPONSE_CODE_DUPLICATE_ACCOUNT){
				return $array;
			}
			return $array;
		}
	}
	public function paymentProfileRequest($customerId, $profileId, $getOrDelete)
	{
		$content = 
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
			"<".$getOrDelete." xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">"
			.$this->merchantAuthenticationBlock().
			"<customerProfileId>".$customerId."</customerProfileId><customerPaymentProfileId>".$profileId."</customerPaymentProfileId></".$getOrDelete.">";
		$response = $this->send_request_via_curl($content);
		if($getOrDelete == $this->_deleteProfileRequest)
		{
			return $this->getResponseArray($response);
		}
		else
		{
			$response = $this->substring_between($response, '<getCustomerPaymentProfileResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">','</getCustomerPaymentProfileResponse>');
			return '<?xml version="1.0" encoding="utf-8"?><getCustomerPaymentProfileResponse>'.$response.'</getCustomerPaymentProfileResponse>';
		}
	}
	function processPaymentWithStoredInfo($amount, $customerId, $paymentId)
	{
		$helper	=	Mage::helper('cim/data');
		$content =
		"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
		$helper->merchantAuthenticationBlock().
		"<transaction>".
		"<profileTransAuthCapture>".
		"<amount>" . $amount ."</amount>". // should include tax, shipping, and everything.
		"<customerProfileId>" . $customerId . "</customerProfileId>".
		"<customerPaymentProfileId>". $paymentId . "</customerPaymentProfileId>".
		"</profileTransAuthCapture>".
		"</transaction>".
		"</createCustomerProfileTransactionRequest>";
		$array	=	$this->getResponseArray($this->send_request_via_curl($content));
		foreach($array as $k=>$v){
			Mage::log($k.' || '.$v);
		}
		return $array;
	}
	
        function updatePaymentProfile($billingData)
        {
                $billingData['cc_exp'] = ($billingData['cc_exp_year'] == "XX" && $billingData['cc_exp_year'] == "XX") 
                ? $billingData['cc_exp_year'].$billingData['cc_exp_month'] : $billingData['cc_exp_year']."-".$this->adjustLength($billingData['cc_exp_month']);

                $content =
                "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<updateCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                $this->merchantAuthenticationBlock().
                "<customerProfileId>".$billingData['cim_id']."</customerProfileId>".
                "<paymentProfile>".
               "<billTo>".
		 "<firstName>".$billingData['firstname']."</firstName>".
		 "<lastName>".$billingData['lastname']."</lastName>".
		 "<address>".$billingData['street']."</address>
			<city>".$billingData['city']."</city>
			<state>".$billingData['region']."</state>
			<zip>".$billingData['postcode']."</zip>".
		 "<phoneNumber></phoneNumber>".
			"<faxNumber></faxNumber>".
		"</billTo>".
		"<payment>".
		 "<creditCard>".
		  "<cardNumber>".$billingData['cc_number']."</cardNumber>".
		  "<expirationDate>".$billingData['cc_exp']."</expirationDate>". // required format for API is YYYY-MM
		 "</creditCard>".
		"</payment>".
                "<customerPaymentProfileId>".$billingData['profile_id']."</customerPaymentProfileId>".
                "</paymentProfile>".
                "</updateCustomerPaymentProfileRequest>";
                //echo '<pre>'.print_r($content, true).'</pre>';
                $response = $this->send_request_via_curl($content);
                $refId = $this->substring_between($response,'<refId>','</refId>');
                $resultCode = $this->substring_between($response,'<resultCode>','</resultCode>');
                $code = $this->substring_between($response,'<code>','</code>');
                $text = $this->substring_between($response,'<text>','</text>');
                $id = $this->substring_between($response,'<customerPaymentProfileId>','</customerPaymentProfileId>');
                return array ('refId' => $refId, 'resultCode' => $resultCode, 'code' => $code, 'text' => $text, 'id' => $id);
        }
	
	public function adjustLength($str){
		return (strlen($str) == 1 ? "0".$str : $str);
	}
	
	
	public $CREDIT_CARD_MAPPINGS = array(
		'VI'	=>	'Visa',
		'MC'	=>	'Mastercard',
		'AE' 	=> 	'American Express',
		'DI'	=>	'Discover'
	);
	
	private function getResponseArray($response){
		$refId 			=	$this->substring_between($response,'<refId>','</refId>');
		$resultCode 	=	$this->substring_between($response,'<resultCode>','</resultCode>');
		$code 			=	$this->substring_between($response,'<code>','</code>');
		$text 			= 	$this->substring_between($response,'<text>','</text>');
		$id 			= 	$this->substring_between($response,'<customerProfileId>','</customerProfileId>');
		$paymentId		=	$this->substring_between($response, '<customerPaymentProfileId>','</customerPaymentProfileId>');
		return array('refId' => $refId, 'resultCode' => $resultCode, 'code' => $code, 'text' => $text, 'id' => $id, 'paymentId' => $paymentId);	
	}
	
	/*
	 * 
	 * Error messages
	 * 
	 */
	public $ERROR_MESSAGE_NO_CUSTOMER_CIM	=	'Could not save credit card; the following customer does not have a CIM customer Id: ';
}
?>
