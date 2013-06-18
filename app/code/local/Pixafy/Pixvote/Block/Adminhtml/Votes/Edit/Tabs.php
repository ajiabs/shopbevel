<?php
class Pixafy_Pixvote_Block_Adminhtml_Votes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('pixvote_tabs');
		$this->setDestElementId('edit_form');
		$pixvoteHelper = Mage::helper("pixvote");
		$challenge = Mage::registry($pixvoteHelper->REGISTRY_CHALLENGE_DATA);
		$this->setTitle($pixvoteHelper->__('View Votes - '.$challenge->getName()));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('form_edit_voters', array(
			'label'		=>	Mage::helper('pixvote')->__('Voters'),
			'title'		=>	Mage::helper('pixvote')->__('Voters'),
			'content'	=>	$this->getLayout()->createBlock('pixvote/adminhtml_votes_edit_tab_voters')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}