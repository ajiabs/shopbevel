<?php
class Pixafy_Pixvote_Block_Adminhtml_Itemtypes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_itemtypes';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Item types');
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Item type');
        parent::__construct();
	}	
}
?>
