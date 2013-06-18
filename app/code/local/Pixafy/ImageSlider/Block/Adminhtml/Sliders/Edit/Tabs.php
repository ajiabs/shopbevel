<?php
class Pixafy_ImageSlider_Block_Adminhtml_Sliders_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Slider Information'));
    }

    protected function _prepareLayout()
    {
		$this->addTab('General Information', array(
			'label'     => Mage::helper('catalog')->__('General Information'),
			'content'   => $this->_translateHtml($this->getLayout()->createBlock('imageslider/adminhtml_sliders_edit_tab_general')->toHtml()),
			'active'    => true
		));
		
		$product = Mage::getModel('catalog/product')->setAttributeSetId(4);
		Mage::register('product', $product);
		
		
		$attrGroupName	=	'Images';
		$read			=	Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql			=	"SELECT attribute_group_id, attribute_set_id, sort_order, default_id FROM eav_attribute_group WHERE attribute_group_name='".$attrGroupName."' ORDER BY attribute_group_id ASC LIMIT 1";
		$res			=	$read->fetchAll($sql);
		if(!empty($res)){
			$res		=	$res[0];
			$groupData	=	array
								(
									"id"	=>	$res['attribute_group_id'], 
									"attribute_group_id" => $res['attribute_group_id'],  
									"attribute_set_id" => $res['attribute_set_id'], 
									"attribute_group_name" => $attrGroupName, 
									"sort_order" => $res['sort_order'], 
									"default_id" => $res['default_id']
								) ;
			$group		=	new Varien_Object();
			$group->setData($groupData);
			$attributes = $product->getAttributes($group->getId(), true);
			//imageslider/adminhtml_sliders_edit_tab_gallery
			$this->addTab('group_'.$group->getId(), array(
						'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),//adminhtml/catalog_product_edit_tab_attributes
						'content'   => $this->_translateHtml(
							$this->getLayout()->createBlock('imageslider/adminhtml_sliders_edit_tab_attributes')
								->setGroup($group)
								->setGroupAttributes($attributes)
								->toHtml()
							),
						)
			);
		}
		else{
			Mage::getSingleton('adminhtml/session')->addError($this->__('Image attribute set could not be found'));
		}
		
		/*
		$this->addTab('Images', array(
			'label'     => Mage::helper('catalog')->__('Images'),
			'content'   => $this->_translateHtml($this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')->toHtml()),
			'active'    => false
		));	
		*/
        return parent::_prepareLayout();
    }

    /**
     * Translate html content
     * 
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
