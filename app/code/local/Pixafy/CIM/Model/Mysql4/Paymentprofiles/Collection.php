<?php
class Pixafy_CIM_Model_Mysql4_Paymentprofiles_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cim/paymentprofiles');
    }
}
