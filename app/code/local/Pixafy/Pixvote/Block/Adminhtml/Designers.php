<?php
class Pixafy_Pixvote_Block_Adminhtml_Designers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_designers';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Designers');
		//$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Challenge');
        parent::__construct();
	}	
}
?>
