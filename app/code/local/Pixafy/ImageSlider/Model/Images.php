<?php
class Pixafy_ImageSlider_Model_Images extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
		parent::_construct();
        $this->_init('imageslider/images');
    }
}
