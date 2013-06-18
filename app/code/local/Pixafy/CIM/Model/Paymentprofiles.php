<?php
class Pixafy_CIM_Model_Paymentprofiles extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
		parent::_construct();
        $this->_init('cim/paymentprofiles');
    }
}
