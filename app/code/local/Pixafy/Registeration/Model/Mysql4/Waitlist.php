<?php
class Pixafy_Registeration_Model_Mysql4_Waitlist extends Mage_Core_Model_Mysql4_Abstract{
	
    protected function _construct(){
        $this->_init('registeration/waitlist', 'entity_id');
    }
    
    public function loadByCustomerId($customerId){
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('cust_id=:cust_id');
           $data = $adapter->fetchRow($select, array('cust_id' => $customerId));
        if(!$data){
            $data = array();
        }
        return $data;
    }
    
    
    
    public function loadByEmail($email){
    	$adapter = $this->_getReadAdapter();
    	$select = $adapter->select()
    	->from($this->getMainTable())
    	->where('cust_email=:cust_email');
    	$data = $adapter->fetchRow($select, array('cust_email' => $email));
    	if(!$data){
    		$data = array();
    	}
    	return $data;
    }
    
    public function loadByKey($key){
    	$adapter = $this->_getReadAdapter();
    	$select = $adapter->select()
    	->from($this->getMainTable())
    	->where('cust_key=:cust_key');
    	$data = $adapter->fetchRow($select, array('cust_key' => $key));
    	if(!$data){
    		$data = array();
    	}
    	return $data;
    }
}