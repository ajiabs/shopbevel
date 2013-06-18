<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Pixafy_Pixlikes_Helper_Data extends Mage_Core_Helper_Abstract
{
	//Get list of prouct user has liked
	function getUserProductLikes($_productId = 0)
	{
		$productIds = array();
		
		if(Mage::helper("customer")->isLoggedIn())
		{
			$_likeProducts = Mage::getModel("pixlikes/productlikes")->getCollection()
			->addFilter("customer_id", Mage::helper("customer")->getCustomer()->getId());
			if($_productId)
			{
				$_likeProducts->addFilter("product_id", $_productId);
			}
			foreach($_likeProducts as $_likedProduct)
			{
				$_likedProductId = $_likedProduct->getProductId();
				$productIds[$_likedProductId] = $_likedProductId;
			}
		}
		return $productIds;
	}
}
?>
