<?php
class Pixafy_ImageSlider_Model_Mysql4_Images_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageslider/images');
    }
}
