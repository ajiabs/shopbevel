<?php
class Pixafy_Registeration_Model_Mysql4_Invitelink extends Mage_Core_Model_Mysql4_Abstract{
	
    protected function _construct(){
        $this->_init('registeration/invitelink', 'entity_id');
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
}