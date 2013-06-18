<?php
class Pixafy_Pixvote_Block_Adminhtml_Entry_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('pixvote_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('pixvote')->__('Entry')); // ?????
	}

	protected function _beforeToHtml()
	{
		
		/*$this->addTab('form_edit_general', array(
			'label'		=>	Mage::helper('pixvote')->__('Entry Information'),
			'title'		=>	Mage::helper('pixvote')->__('Entry Information'),
			'content'	=>	$this->getLayout()->createBlock('pixvote/adminhtml_entry_edit_tab_general')->toHtml(),
		));
		*/
		$this->addTab('form_edit_edit', array(
			'label'		=>	Mage::helper('pixvote')->__('Edit Entry Information'),
			'title'		=>	Mage::helper('pixvote')->__('Edit Entry Information'),
			'content'	=>	$this->getLayout()->createBlock('pixvote/adminhtml_entry_edit_tab_form')->toHtml(),
		));
//		$this->addTab('form_edit_vote', array(
//			'label'		=>	Mage::helper('pixvote')->__('Edit Entry Vote Time'),
//			'title'		=>	Mage::helper('pixvote')->__('Edit Entry Vote Time'),
//			'content'	=>	$this->getLayout()->createBlock('pixvote/adminhtml_entry_edit_tab_vote')->toHtml(),
//		));
//		
		$this->addTab('form_edit_images', array(
			'label'		=>	Mage::helper('pixvote')->__('Submission images'),
			'title'		=>	Mage::helper('pixvote')->__('Submission images'),
			'content'	=>	$this->getLayout()->createBlock('pixvote/adminhtml_entry_edit_tab_images')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}