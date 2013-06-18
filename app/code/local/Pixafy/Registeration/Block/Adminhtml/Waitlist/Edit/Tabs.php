<?php

class Pixafy_Registeration_Block_Adminhtml_Waitlist_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('registeration_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('registeration')->__('Wait List Information'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('registeration')->__('wait list info'),
            'title'     => Mage::helper('registeration')->__('wait list info'),
            'content'   => $this->getLayout()->createBlock('registeration/adminhtml_waitlist_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}
