<?php
class Pixafy_Pixvote_Block_Adminhtml_Designtips extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_designtips';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Tip of the day');
		//$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Challenge');
        parent::__construct();
	}	
}
?>
