<?php
	class Pixafy_Bevel_Block_Home extends Mage_Core_Block_Template
	{
		function __construct()
		{
			$pixvoteHelper = Mage::helper("pixvote");
			$preOrderCollectionId = Mage::getStoreConfig($pixvoteHelper->IN_PRODUCTION_CATEGORY_ID_PATH);
			$collectionParent = Mage::getModel('catalog/category')->load(Mage::getStoreConfig('bevel/general/collection_category_id'));
			$collectionIds = $collectionParent->getChildren();
			$filter = array("in" => explode(',',$collectionIds));
			$collections = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect(array('thumbnail','image_url'))
			->addAttributeToFilter('entity_id', $filter)
			->addAttributeToFilter('is_on_homepage', $pixvoteHelper->VAL_TRUE)
			->setOrder('slider_position','ASC');
			$this->setCollections($collections);
			$visiblity = array(
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
				Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
			);
			
			
			//Get topselling products
			$productAttrs = array('price', 'special_price');
			$this->setProductAttrs($productAttrs);
			//$productAttrs = '*';
			$homeProductLimit = $this->helper('bevel/shopbevel')->_homeProductLimit;
			
			$topProductsManual =  Mage::getModel("catalog/product")->getCollection()
			->addAttributeToFilter("top_seller",$pixvoteHelper->VAL_TRUE)
			->addAttributeToSelect($productAttrs)
			->setPageSize($homeProductLimit);
	
			$topProducts = array();
			
			foreach($topProductsManual as $topProduct)
			{
				$topProducts[$topProduct->getId()] = $topProduct;
			}
			
			if(count($topProducts) < $homeProductLimit)
			{
				$topProductsAuto = Mage::getResourceModel("reports/product_collection")
				->addOrderedQty()
				->addAttributeToSelect($productAttrs)
				->addAttributeToFilter('visibility',array("in" => $visiblity))
				//->setPageSize($homeProductLimit)
				->addCategoryFilter($collectionParent)
				->setOrder('ordered_qty','DESC');
				foreach($topProductsAuto as $topProduct)
				{
					if(!isset($topProducts[$topProduct->getId()]) && !in_array($preOrderCollectionId,$topProduct->getCategoryIds()))
					{
						$topProducts[$topProduct->getId()] = $topProduct;
					}
					
					if(count($topProducts) == $homeProductLimit)
					{
						break;
					}
				}
			}
			
			
			//Get new Arrival products
			$newProductsManual =  Mage::getModel("catalog/product")->getCollection()
			->addAttributeToSelect($productAttrs)
			->addAttributeToFilter("new_arrival",$pixvoteHelper->VAL_TRUE)
			->setPageSize($homeProductLimit);
	
			$newProducts = array();
			
			foreach($newProductsManual as $newProduct)
			{
				$newProducts[$newProduct->getId()] = $newProduct;
			}
			
			if(count($newProducts) < $homeProductLimit)
			{
				$newProductsAuto = Mage::getModel("catalog/product")->getCollection()
				->addAttributeToSelect($productAttrs)
				->setOrder("entity_id", "DESC")
				->addAttributeToFilter('visibility',array("in" => $visiblity))
				->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
				//->setPageSize($homeProductLimit);
				foreach($newProductsAuto as $newProduct)
				{
					//$isChild = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($newProduct->getId());
					if(!isset($newProducts[$newProduct->getId()]) && !in_array($preOrderCollectionId,$newProduct->getCategoryIds()))
					{
						$newProducts[$newProduct->getId()] = $newProduct;	
					}
					
					if(count($newProducts) == $homeProductLimit)
					{
						break;
					}
				}
			}
			
			//Get preorder products
			
			$preorderCategory = Mage::getModel('catalog/category')->load($preOrderCollectionId);
			
			$preorderCollection = $preorderCategory->getProductCollection()
			->addAttributeToSelect($productAttrs)
			->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addAttributeToFilter('visibility',array("in" => $visiblity))
			->setPageSize($homeProductLimit)
			->setOrder("entity_id", "DESC");
			
			$productAttrs[] = 'small_image';
			$sliderProducts = Mage::getModel("catalog/product")->getCollection()
			->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addAttributeToFilter('visibility',array("in" => $visiblity))
			->addAttributeToFilter('is_on_homepage', 1)
			->addAttributeToSelect($productAttrs);
			
			$this->setTopProducts($topProducts);
			$this->setNewProducts($newProducts);
			$this->setPreorderProducts($preorderCollection);
			$this->setSliderProducts($sliderProducts);
		}
	}
?>