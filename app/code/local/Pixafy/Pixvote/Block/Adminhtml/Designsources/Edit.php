<?php
class Pixafy_Pixvote_Block_Adminhtml_Designsources_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pixvote';
		$this->_controller = 'adminhtml_designsources';
		$pixvoteHelper = Mage::helper("pixvote");
		$source = Mage::registry($pixvoteHelper->REGISTRY_DESIGNSOURCE_KEY);
		$this->_addButton('save_and_continue', array(
                  'label' => $pixvoteHelper->__('Save And Continue Edit'),
                  'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                  'class' => 'save',
        ), -100);
		 
		$this->_headerText = $pixvoteHelper->__('Edit Form - '.$source->getId());
	}
}