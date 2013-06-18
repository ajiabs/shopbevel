<?php
class Pixafy_Pixvote_Block_Adminhtml_Votes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pixvote';
		$this->_controller = 'adminhtml_votes';
		$this->_headerText = Mage::helper('pixvote')->__('View votes');
	}
}