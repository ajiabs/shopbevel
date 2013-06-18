<?php
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('imageslider/sliders/tab/images.phtml');
    }
    
    
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_fieldset_element')
        );
        $this->getLayout()->createBlock('imageslider/adminhtml_sliders_edit_tab_images_gallery')->toHtml();
    }    
}
