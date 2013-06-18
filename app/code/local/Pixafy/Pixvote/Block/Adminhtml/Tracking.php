<?php
class Pixafy_Pixvote_Block_Adminhtml_Tracking extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_tracking';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('A/B stats');
        parent::__construct();
		$this->_removeButton('add'); 
	}	
}
?>
