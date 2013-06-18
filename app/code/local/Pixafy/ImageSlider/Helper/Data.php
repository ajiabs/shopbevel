<?php
class Pixafy_ImageSlider_Helper_Data extends Mage_Core_Helper_Abstract {
	public $_sliderPath;
	public $_fullPath;
	public function __construct(){
		$this->_sliderPath	=	'imageslider/';
		$this->_fullPath	=	Mage::getBaseDir().'/'.$this->_sliderPath;
	}
	
	
	
	
	public function deleteSlider($sliderId){
		if(is_numeric($sliderId)){
			$images	=	Mage::getModel('imageslider/images')->getCollection()->addFieldToFilter('slider_id', $sliderId);
			foreach ($images as $img){
				$img->delete();
				unlink($this->_fullPath.$img->getImagePath());
			}
			
			$slider	=	Mage::getModel('imageslider/sliders')->load($sliderId);
			$slider->delete();
		}
	}
}
?>
