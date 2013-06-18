<?php
class Pixafy_ImageSlider_Block_Slider extends Mage_Core_Block_Template{
	protected $_images;
	protected $_mageClean;
	protected $_sliderId;
	protected $_loaded	=	FALSE;
	protected $_constructData;
	protected $_slider;
	protected $_id;
	protected $_sliderPath;
	protected $_isDisabled	=	FALSE;
	protected $_registryKey	=	'imageslider_registered';
	
	public $_yesNo	=	array('Yes'=>1, 'No'=>0);
	
	public function __construct($data){
		$this->_constructData	=	$data;
		$this->_mageClean		=	Mage::getBaseUrl('media');
		$this->_sliderPath		=	Mage::helper('imageslider')->_sliderPath;
	}
	private function loadImages(){
		$this->_slider	=	Mage::getModel("imageslider/sliders")->load($this->_sliderId);
		if($this->_yesNo[$this->_slider->getIsActive()]){
			$this->_images	=	Mage::getModel("imageslider/images")->getCollection()->setOrder('sort_order', 'ASC')->addFieldToFilter('is_active', 1)->addFieldToFilter('slider_id', $this->_sliderId);
		}
		else{
			$this->_isDisabled	=	TRUE;
		}
		$this->_loaded	=	TRUE;
	}
	public function setSliderId($id=NULL){
		if(!$id){
			$id	=	$this->_constructData['id'];
		}
		if(!$id){
			$id	=	$this->_id;
		}
		$this->_sliderId	=	$id;
		$this->loadImages();
	}
	public function setId($id){
		$this->_id	=	$id;
		return $this;
	}
	protected function getDelay(){
		$delay	=	$this->_slider->getSeconds();
		if(!is_numeric($delay)){
			$delay	=	5;
		}
		return $delay;
	}
	protected function getWidth(){
		$width	=	$this->_slider->getSliderWidth();
		if($width	==	0 ||	!is_numeric($width)){
			return FALSE;
		}
		return $width;
	}
	protected function getHeight(){
		$height	=	$this->_slider->getSliderHeight();	
		if($height	==	0 ||	!is_numeric($height)){
			return FALSE;
		}
		return $height;
	}
	
	protected function getResizedImage($img, $width = 988, $height = NULL)
	{
		$fileName = $img->getImagePath();
		if (!$fileName)
		{
			return "";	
		}
		$resizePrefix = $width.(empty($height) ? "x_" : "x".$height.'_');
		$imageUrl =  Mage::getBaseDir ( 'media' ) . DS . "imageslider" . DS . $fileName;
		if (! is_file ( $imageUrl ))
		{
			return "";	
		}
		$imageResized = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "product" . DS . "cache" . DS . "sldier_resized" . DS . $resizePrefix. $fileName;
		// Because clean Image cache function works in this folder only
		
		if (! file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) :
		$imageObj = new Varien_Image ( $imageUrl );
		$imageObj->constrainOnly ( true );
		$imageObj->keepAspectRatio ( true );
		$imageObj->keepFrame ( false );
		$imageObj->quality ( $quality );
		$imageObj->resize ( $width, $height );
		$imageObj->save ( $imageResized );
		endif;

		if(file_exists($imageResized))
		{
			
			return Mage::getBaseUrl ( 'media' ) ."/catalog/product/cache/sldier_resized/" . $resizePrefix . $fileName;
			//return Mage::getBaseUrl ( 'media' ) ."/catalog/product/cache/sldier_resized/"  . $fileName;
		}
		else
		{
			$imageUrl;
		}
	}
}
?>
