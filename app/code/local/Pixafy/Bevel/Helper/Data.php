<?php
	class Pixafy_Bevel_Helper_Data extends Mage_Core_Helper_Abstract 
	{
		//class constants
		public $_share_text;
		public $_fb_app_id;
		public $_fb_app_secrect;
		/**
		 * @var string url to the wordpress login page
		 */
		public $pre_reg_url = 'http://10.0.1.229/bevel/wp/';
		
		function __construct()
		{
			$this->_share_text = 'I voted to produce this: %s. Vote too!';
			$this->_share_text_general = 'Check this out on Shopbevel! ';
			$this->_fb_app_id = (string)Mage::getStoreConfig('optimus_social_facebook/general/facebook_app_id');
			$this->_fb_app_secret = (string)Mage::getStoreConfig('optimus_social_facebook/general/facebook_app_secret');
		}

        function generateShareText($productName) {
            return sprintf($this->_share_text, $productName);
        }
		
		//Check if to use plura or singular version of word
		function pluralize($num, $singular, $plural)
		{
		    return $num == 1 ? $this->__($singular) : $this->__($plural);
		}
	}
?>