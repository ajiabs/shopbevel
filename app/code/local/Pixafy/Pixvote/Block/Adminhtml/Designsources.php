<?php
class Pixafy_Pixvote_Block_Adminhtml_Designsources extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_designsources';
		$this->_blockGroup = 'pixvote';
		$this->_headerText = Mage::helper('adminhtml')->__('Submission Sources');
		//$this->_addButtonLabel = Mage::helper('adminhtml')->__('Create Challenge');
        parent::__construct();
	}	
}
?>
