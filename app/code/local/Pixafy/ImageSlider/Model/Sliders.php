<?php
class Pixafy_ImageSlider_Model_Sliders extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
		parent::_construct();
        $this->_init('imageslider/sliders');
    }
}
