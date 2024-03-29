<?php
class Mage_Checkout_Block_Onepage_Excellence extends Mage_Checkout_Block_Onepage_Abstract{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('excellence', array(
            'label'     => Mage::helper('checkout')->__('Customer'),
            'is_show'   => $this->isShow()
        ));
        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('excellence', 'allow', true);
            $this->getCheckout()->setStepData('billing', 'allow', false);
        }
 
        parent::_construct();
    }
}