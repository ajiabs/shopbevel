<?php
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Form
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        if ($group = $this->getGroup()) {
            $form = new Varien_Data_Form();
            
            /**
             * Initialize product object as form property
             * for using it in elements generation
             */
            $form->setDataObject(Mage::registry('product'));
            $fieldset = $form->addFieldset('group_fields'.$group->getId(),
                array(
                    'legend'=>Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'class'=>'fieldset-wide',
            ));
            $attributes = $this->getGroupAttributes();
            $this->_setFieldset($attributes, $fieldset, array('gallery'));			
            $values = Mage::registry('product')->getData();
            $form->addValues($values);
            $form->setFieldNameSuffix('product');
            $this->setForm($form);
        }
    }

    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'gallery'  => Mage::getConfig()->getBlockClassName('imageslider/adminhtml_sliders_edit_tab_gallery'),
        );
        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response'=>$response));
        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }
        return $result;
    }
}
