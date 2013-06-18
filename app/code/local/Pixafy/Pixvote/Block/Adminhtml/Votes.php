<?php
class Pixafy_Pixvote_Block_Adminhtml_Votes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_votes';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Challenge Votes');
        parent::__construct();
		$this->_removeButton('add'); 
	}	
}
?>
