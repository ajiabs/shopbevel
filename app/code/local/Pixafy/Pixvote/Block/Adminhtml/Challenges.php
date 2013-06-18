<?php
class Pixafy_Pixvote_Block_Adminhtml_Challenges extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_challenges';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Challenges');
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Challenge');
        parent::__construct();
	}	
}
?>
