<?php
class Pixafy_Pixvote_Block_Adminhtml_Designtips_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pixvote';
		$this->_controller = 'adminhtml_designtips';
		$tip = Mage::registry("designtip_data");
		$this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('pixvote')->__('Save And Continue Edit'),
                  'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                  'class' => 'save',
        ), -100);
		 
		$this->_headerText = Mage::helper('pixvote')->__('Edit Form - '.$tip->getId());
	}
}