<?php
class Pixafy_CIM_Model_Mysql4_Paymentprofiles extends Mage_Core_Model_Mysql4_Abstract
{   
    public function _construct()
    {
        $this->_init('cim/paymentprofiles', 'id');
    }
}
