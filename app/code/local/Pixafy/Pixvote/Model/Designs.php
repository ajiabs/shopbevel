<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class Pixafy_Pixvote_Model_Designs extends Mage_Core_Model_Abstract
 {
    public function _construct()
    {	
		parent::_construct();
        $this->_init('pixvote/designs');
    }
	
	//Converts design to simple product
	public function _convertToProduct()
	{
		$id = $this->getId() < 100 ? '0'.$this->getId() : $this->getId();
		$sku = "SBD".$id;
		$pixvoteHelper = Mage::helper('pixvote');
		$shopbevelHelper = Mage::helper('bevel/shopbevel');
		$exsiting = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if(!$exsiting)
		{
			//Get collection categories
			$collectionParentId = Mage::getStoreConfig($pixvoteHelper->COLLECTION_CATEGORY_ID_PATH);
			$inProductionCategoryId = Mage::getStoreConfig($pixvoteHelper->IN_PRODUCTION_CATEGORY_ID_PATH);
			
			//Get designer category or create one if it does not exists
			$designerCategory = Mage::getModel('catalog/category')->loadByAttribute("designer_id", $this->getCustomerId());
			if(!$designerCategory)
			{
				//Get designer parent category
				$designerParentId = $shopbevelHelper->getDesignerCategoryId();
				$designerParentCategory = Mage::getModel('catalog/category')->load($designerParentId);
				
				//create category
				$designer = Mage::getModel("customer/customer")->load($this->getCustomerId());
				$designerCategory = Mage::getModel('catalog/category');
				$designerCategory->setIsActive(1);
				$designerCategory->setIncludeInMenu(0);
				$designerCategory->setName($designer->getName());
				$designerCategory->setDescription($designer->getDescription());
				$designerCategory->setDesigner($designer->getName());
				$designerCategory->setLocation($designer->getLocation());
				$designerCategory->setDesignerId($designer->getId());
				$designerCategory->setUrlKey('designer-'.$designer->getId());
				$designerCategory->setPath($designerParentCategory->getPath());  
				
				//Copy profile image if avaiable
				if($designer->getProfileImage())
				{
					$customerImagePath = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.$designer->getProfileImage();
					$fileName = pathinfo($customerImagePath,PATHINFO_BASENAME);
					$categoryImagePath = Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.'catalog'.DS.'category'.DS.$fileName;
					copy($customerImagePath,$categoryImagePath);
					$designerCategory->setThumbnail($fileName);
				}
				
				//Save category
				$designerCategory->save();
			}
			
			$categoryIds = array($inProductionCategoryId, $designerCategory->getId());
			
			//Get discount amount;
			$discountPercentage = Mage::getStoreConfig($pixvoteHelper->IN_PRODUCTION_DISCOUNT_PATH)/100;
			$specialPrice = round($this->getBevelPrice() - ($this->getBevelPrice() * $discountPercentage));
			
			$attributeSetId=4; //change to the appropriate id
			$product = Mage::getModel('catalog/product');
			$product->setTypeId('simple'); //virtual, configurable, etc
			$product->setCategoryIds($categoryIds);
			$product->setAttributeSetId($attributeSetId);
			$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
			$product->setStockData(array(
				'manage_stock' => $pixvoteHelper->VAL_TRUE,
				'qty' => Mage::getStoreConfig($pixvoteHelper->IN_PRODUCTION_DEFAULT_QTY_PATH),
				'is_in_stock' => $pixvoteHelper->VAL_TRUE
			));
			$product->setName($this->getName());
			$product->setMaterials($this->setMaterials());
			$product->setDescription($this->getDescription());
			$product->setShortDescription($this->getDescription());
			$product->setWebsiteIds(array(1)); // or any other website ids
			$product->setStoreIds(array(1)); // or any other store id
			$product->setPrice($this->getBevelPrice());
			$product->setSpecialPrice($specialPrice);
			$product->setTaxClassId(2); //for no taxes. the id is 2 for standard tax class
			$product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			$product->setSku($sku);
			if($this->getColor())
			{
				$product->setColor($this->getColor());
			}
			$product->setData('is_made_by_bevel', $pixvoteHelper->VAL_TRUE);
			$product->setData('design_id', $this->getId());
			//$product->setMediaGallery (array('images'=>array (), 'values'=>array()));
			try
			{
				$product->save();
				//Add images to gallery, if any
				$images = Mage::getModel($pixvoteHelper->DESIGN_IMAGES_MODEL_PATH)->getCollection()
				->addFilter('design_id', $this->getId());
				if(count($images))
				{
					//reload Product to get full model
					$product = Mage::getModel("catalog/product")->load($product->getId());
					$isFirst = true;
					foreach($images as $image)
					{
						$imageFilePath =  Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA).DS.$image->getUrl();
						if($isFirst)
						{
							$product->addImageToMediaGallery($imageFilePath, array('image','small_image','thumbnail'), false, false);
							$isFirst = false;
						}
						else
						{
							$product->addImageToMediaGallery ($imageFilePath, array(), false, false);
						}
					}
					$product->save();
				}
				
			}
			catch(Mage_Core_Exception $e){

			}
			catch(Exception $e){

			}
			return $product;
		}
		else
		{
			return false;
		}
	}
 }
?>
