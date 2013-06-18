<?php
class Pixafy_Registeration_Model_Invitelink extends Mage_Core_Model_Abstract {
	
    protected function _construct(){
        $this->_init('registeration/invitelink');
    }
  
	public function loadByCustomerId($customerId){
		$this->addData($this->getResource()->loadByCustomerId($customerId));
        return $this;
    }
    
    
    //This is for updating 'created_at', 'updated_at' and 'store_id'
    protected function _beforeSave(){
		$datetime = date('Y-m-d H:i:s');
    	if(!$this->getId()){
    		$this->setData('created_at', $datetime);
    	}
    	$this->setData('updated_at', $datetime);
    	if(!$this->getStoreId()){
    		$this->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
    	}
    	parent::_beforeSave();
    }
    
}