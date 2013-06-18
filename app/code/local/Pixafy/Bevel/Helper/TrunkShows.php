<?php
	class Pixafy_Bevel_Helper_TrunkShows extends Mage_Core_Helper_Abstract 
	{
		public $_startDate;
		public $_startTime;
		
		function __construct()
		{
			$this->_startDate = 14;
			$this->_startTime = "12:00 pm";
		}
		
		//Get trunk shows
		public function getTrunkShows($excludeId = FALSE, $limit = FALSE, $getPastOnly = false)
		{
			$_events = Mage::getModel('enterprise_catalogevent/event')
			->getCollection()
			->addOrder('date_end','ASC');
			
			if($getPastOnly)
			{
				$_events->addFieldToFilter("status", 'closed');
			}
			else
			{
				$_events->addFieldToFilter("status", 'open');
			}
			if($excludeId)
			{
				$_events->addFieldToFilter("event_id", array('neq' => $excludeId));
			}
			if($limit)
			{
				$_events->setPageSize($limit);
			}
			$_events->load();

			return $_events;
		}

		/**
		 * Get the first featured trunk show (sort_order == 1) 
		 * 
		 * @return catalogevent model
		 */
		public function getFeaturedTrunkShow() {
			$_event = Mage::getModel('enterprise_catalogevent/event')->load('1', 'sort_order');
			
			return $_event;
		}
		
		public function getProductStock($_product)
		{
			return (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
		}
		
		//Get prev and next products for pagination
		public function getPrevNextProduct($_category, $_currentProductId)
		{
			$_productsCollection = $_category->getProductCollection()->addAttributeToSort('position');
			
			//convert to index-based array
			$_products = array();
			foreach($_productsCollection as $_product)
			{
				$_products[] = $_product;
			}
			
			//Get previous and next products in collection
			$_count = 0;
			$_prevNextArr = array('_prevProduct' => '', '_nextProduct' => '');
			foreach($_products as $key => $_product)
			{
				if($_product->getId() == $_currentProductId)
				{
					if($_count)
					{
						$_prevNextArr['_prevProduct'] = $_products[$_count - 1];
					}
					if(($count+1) != count($_products))
					{
						$_prevNextArr['_nextProduct'] = $_products[$_count + 1];
					}
					break;
				}
				$_count++;
			}
			return $_prevNextArr;
		}
		
		public function getIsSellable($_product, $_event)
		{
			return $_product->isSaleable() || $_product->getIsPopular() || $_event->getStatus() == "open";
		}
	}
?>