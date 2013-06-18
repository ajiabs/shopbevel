<?php
class Pixafy_CIM_Block_Payment_Savecard extends Mage_Core_Block_Template
{
	protected $autosaveCards;
    public function __construct()
    {
        $this->autosaveCards	=	Mage::getStoreConfig('payment/cimmethod/card_autosave');
    }
    
    protected function userCanSaveCards(){
		if(!$this->autosaveCards){
			return TRUE;
		}
		return FALSE;
	}
}
