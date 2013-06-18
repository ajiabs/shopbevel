<?php
class Pixafy_Pixvote_Block_Adminhtml_Entry extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function _construct()
	{
		$this->_controller = 'adminhtml_entry';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Design Entries');
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Entry');
                 parent::_construct();
	}	
}
?>
