<?php

class Pixafy_Registeration_Block_Adminhtml_Waitlist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'registeration';
        $this->_controller = 'adminhtml_waitlist';
 
        $this->_updateButton('save', 'label', Mage::helper('registeration')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('registeration')->__('Delete Item'));
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('registeration_data') && Mage::registry('registeration_data')->getId() ) {
            return Mage::helper('registeration')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('registeration_data')->getId()));
        } else {
            return Mage::helper('registeration')->__('Add Item');
        }
    }
			    
}
