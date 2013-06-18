<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class Pixafy_Pixlikes_IndexController extends Mage_Core_Controller_Front_Action
 {
	 function _construct()
	 {
		 define("ERROR_LOGIN", $this->__("Please login to continue"));
		 define("ERROR_INVALID_DATA", $this->__("No valid data has been passed"));
		 define("ERROR_PRODUCT_LIKED_ALREADY", $this->__("You have liked this product already"));
	 }
	 function indexAction()
	 {
		 
	 }
	 
	 function likeAction()
	 {
		 $_json = array();
		 if(Mage::helper("customer")->isLoggedIn())
		 {
			$_customer = Mage::helper("customer")->getCustomer();
			$_productId = $this->getRequest()->getParam("id");
			$_product = Mage::getModel("catalog/product")->load($_productId);
			if($_product->getId())
			{
				//Check if record exits
				$_json['id'] = $_product->getId();
				$existing = Mage::getModel("pixlikes/productlikes")->getCollection()
				->addFilter("customer_id", $_customer->getId())
				->addFilter("product_id", $_productId);
				if(!count($existing))
				{
					//Create record update count
					$data = array(
						"customer_id" => $_customer->getId(),
						"product_id" => $_productId
					);
					$record = Mage::getModel("pixlikes/productlikes")->setData($data);
					$record->save();
					
					$likeCount = $_product->getFavCount();
					$likeCount++;
					$_product->setFavCount($likeCount);
					$_product->save();
					$_json['like_count'] = $likeCount;
				}
				else
				{
					$_json['error'] = ERROR_PRODUCT_LIKED_ALREADY;
				}
			}
			else
			{
				$_json['error'] = ERROR_INVALID_DATA;
			}
		 }
		 else
		 {
			 $_json['error'] = ERROR_LOGIN;
		 }
		 echo json_encode($_json);
	 }
 }
?>
