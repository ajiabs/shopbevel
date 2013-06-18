<?php 


class Pixafy_Registeration_Block_Adminhtml_Waitlist extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
		$this->_blockGroup	=	'registeration';
        $this->_controller = 'adminhtml_waitlist';
        $this->_headerText = Mage::helper('customer')->__('Manage Waitlist');
        parent::__construct();
    }

}