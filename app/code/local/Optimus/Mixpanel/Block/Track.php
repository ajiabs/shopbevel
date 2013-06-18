<?php
/**
* Optimus Mixpanel block
*
* @codepool   Local
* @category   Optimus
* @package    Optimus_Mixpanel
* @module     Mixpanel
*/
class Optimus_Mixpanel_Block_Track extends Mage_Core_Block_Template
{
    public function getMixpanelOn() {
  		  return (boolean)Mage::getStoreConfig('mixpanel/track/mixpanel_on');
    }

    public function getMixpanelCode() {
          return (string)Mage::getStoreConfig('mixpanel/track/mixpanel_code');
    }

    public function getMixpanelName() {
          return (boolean)Mage::getStoreConfig('mixpanel/track/mixpanel_name');
    }

    protected function _getOrdersDetails() {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return 'false';
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
        ;
        $result = array('revenue' => 0, 'items' => array());
        foreach ($collection as $order) {
//            $resultItem = array(
//                'id' => $order->getIncrementId(),
//                'base grand total' => $order->getBaseGrandTotal()
//            );
            foreach ($order->getAllVisibleItems() as $item) {
                $result['items'][] = $item->getSku();
            }
//            $result['orders'][] = $resultItem;
            $result['revenue'] += $order->getBaseGrandTotal();
        }
        return json_encode($result);
    }

}
