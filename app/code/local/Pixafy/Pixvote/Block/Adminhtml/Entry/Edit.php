<?php
class Pixafy_Pixvote_Block_Adminhtml_Entry_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pixvote';
		$this->_controller = 'adminhtml_entry';
		$this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('pixvote')->__('Save And Continue Edit'),
                  'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                  'class' => 'save',
        ), -100);

//		$this->_updateButton('save', 'label', Mage::helper('pixvote')->__('Save Item'));
//		$this->_updateButton('delete', 'label', Mage::helper('pixvote')->__('Delete Item'));
//                $this->_addButton('resend', array(
//                    'label'     => Mage::helper('pixvote')->__('Resend Email'),
//                    'onclick'   => 'editForm.submit();',
//                    'class'     => 'save',
//                ), 0);
		$this->_headerText = Mage::helper('pixvote')->__('Edit Form');
	}

	public function getHeaderText()
	{
//		if(Mage::registry('pixpurchaseorder_data') && Mage::registry('pixpurchaseorder_data')->getId())
//		{
//			return Mage::helper('pixpurchaseorder')->__("Edit Item %s", $this->htmlEscape(Mage::registry('pixpurchaseorder_data')->getId()));
//		}
//		else
//		{
//			return Mage::helper('pixpurchaseorder')->__('Add Item');
//		}
	}
}