<?php
/**
 * Optimus Mixpanel module observer
 *
 * @category   Optimus
 * @package    Optimus_Mixpanel
 */
class Optimus_Mixpanel_Model_Observer {
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function trackOrderSuccess(Varien_Event_Observer $observer) {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('mixpanel-brandid');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
