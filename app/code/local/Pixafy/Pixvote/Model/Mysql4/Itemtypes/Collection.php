<?php
class Pixafy_Pixvote_Model_Mysql4_Itemtypes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pixvote/itemtypes');
    }
}
?>
