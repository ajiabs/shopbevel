<?php
class Pixafy_ImageSlider_Block_Adminhtml_Sliders extends Mage_Adminhtml_Block_Widget_Container{
    public function __construct(){
            parent::__construct();
    }
    
    /**
     * Prepare button and grid
     *
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
		
		//$this->_addButton('add_new', array(
        //    'label'   => Mage::helper('catalog')->__('Add Product'),
        //    'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
        //    'class'   => 'add'
        //));
        $this->setChild('grid', $this->getLayout()->createBlock('imageslider/adminhtml_sliders_grid', 'sliders.grid'));
        return parent::_prepareLayout();
    }
    /**
     * Deprecated since 1.3.2
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }    
}
