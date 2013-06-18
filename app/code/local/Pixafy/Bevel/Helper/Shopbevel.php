<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Shopbevel
 *
 * @author eetienne
 */
class Pixafy_Bevel_Helper_Shopbevel extends Mage_Core_Helper_Abstract
{
	public $_resizeWidth;
	public $_resizeHeight;
	public $_resizeWidthHome;
	public $_resizeHeightHome;
	public $_homeProductLimit;
	function __construct()
	{
		$this->_resizeWidth = 270;
		$this->_resizeHeight = 180;
		$this->_resizeWidthHome = 140;
		$this->_resizeHeightHome = 95;
		$this->_homeProductLimit = 8;
	}
	public function getVoteDate()
	{
		return Mage::getStoreConfig('bevel/general/vote_start_date');
	}
	
	public function getDesignerCategoryId()
	{
		return Mage::getStoreConfig('bevel/general/designer_category_id');
	}
	
	public function getLoginDisplayTime()
	{
		return Mage::getStoreConfig('bevel/general/login_display_time');
	}
	
	public function getCaptchaKey($type)
	{
		return Mage::getStoreConfig('bevel/general/google_captcha_'.$type.'_key');
	}
	
	//Retrieve Designer category incase one was not already set
	public function getProductDesigner($_product)
	{
		$_category = $_product->getCategory();
		$_parentId = $this->getDesignerCategoryId();
		$_category = $this->getCategoryByParent($_product, $_category, $_parentId);
		return $_category;
	}
	
	public function getCategoryByParent($_product, $_category, $_parentId)
	{
		if(is_null($_category) || $_category->getParentId() != $_parentId) 
		{
			$_pixvoteHelper = Mage::helper("pixvote");
			$_catIds = $_product->getCategoryIds();
			foreach($_catIds as $_catId)
			{
				$_registryKey = $_pixvoteHelper->REGISTRY_CATEGORY_KEY.$_catId;
				if(!($_category = Mage::registry($_registryKey)))
				{
					$_category = Mage::getModel('catalog/category')->load($_catId);
					Mage::register($_registryKey, $_category);
					
				}
				
				if($_category->getParentId() == $_parentId)
				{
					break;
				}
			}
		}
		return $_category;
	}
	
	public function getCategoryThumbnail($_category)
	{
		return Mage::getBaseUrl('media').'catalog/category/'.$_category->getThumbnail();
	}
	
	public function checkCaptchaResponse($challengeField, $responseField)
	{
		require_once("scripts/recaptcha/recaptcha.php");
		return recaptcha_check_answer (
			$this->getCaptchaKey('private'),
			Mage::helper('core/http')->getRemoteAddr(),
			$challengeField,
			$responseField
		);
	}
}

?>
