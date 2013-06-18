<?php
class Pixafy_ImageSlider_Model_Mysql4_Images extends Mage_Core_Model_Mysql4_Abstract
{   
    public function _construct()
    {
        $this->_init('imageslider/images', 'id');
    }
}
